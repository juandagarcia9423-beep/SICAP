<?php require_once '../views/layouts/auth_header.php'; ?>

<style>
    #webcam-container {
        position: relative;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        background: #000;
        display: none;
    }
    #video-feed {
        width: 100%;
        height: auto;
        display: block;
    }
    #overlay-canvas {
        position: absolute;
        top: 0;
        left: 0;
    }
    .status-msg {
        margin-top: 1rem;
        font-weight: 600;
        font-size: 0.9rem;
    }
    #loading-models {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 2rem;
    }
    .spinner {
        border: 4px solid rgba(0,0,0,0.1);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border-left-color: #3b82f6;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="auth-card" style="width: 450px;">
    <h2>Reconocimiento Facial</h2>

    <!-- Paso 1: Identificación -->
    <div id="step-identification">
        <p>Ingrese su número de cédula para iniciar el escaneo:</p>
        <div class="form-group" style="text-align: left; margin-bottom: 1.5rem;">
            <label>Cédula</label>
            <input type="text" id="cedula-input" class="form-control" placeholder="Ej: 12345678" autofocus>
        </div>
        <button type="button" id="btn-start-verification" class="btn btn-success">Continuar</button>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="<?php echo URLROOT; ?>/auth/metodos" class="btn" style="background-color: #f59e0b; color: white; padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 4px;">Cambiar Método</a>
        </div>
    </div>

    <!-- Paso 2: Escaneo -->
    <div id="step-verification" style="display: none;">
        <div id="loading-models">
            <div class="spinner"></div>
            <span>Cargando sistema de visión...</span>
        </div>

        <div id="webcam-container">
            <video id="video-feed" autoplay muted playsinline></video>
            <canvas id="overlay-canvas"></canvas>
        </div>

        <div id="verification-status" class="status-msg" style="color: #64748b;">
            Esperando identificación...
        </div>

        <button type="button" id="btn-cancel" class="btn btn-danger" style="margin-top: 1rem;">Cancelar</button>
    </div>
</div>

<!-- Scripts de Face-API.js -->
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>

<script>
    const identificationStep = document.getElementById('step-identification');
    const verificationStep = document.getElementById('step-verification');
    const webcamContainer = document.getElementById('webcam-container');
    const loadingModels = document.getElementById('loading-models');
    const video = document.getElementById('video-feed');
    const canvas = document.getElementById('overlay-canvas');
    const statusMsg = document.getElementById('verification-status');
    const cedulaInput = document.getElementById('cedula-input');
    const btnStart = document.getElementById('btn-start-verification');
    const btnCancel = document.getElementById('btn-cancel');

    let modelsLoaded = false;
    let faceMatcher = null;
    let labeledDescriptors = null;
    let stream = null;
    let detectionInterval = null;

    // Cargar modelos de face-api.js
    async function loadModels() {
        const MODEL_URL = 'https://raw.githubusercontent.com/vladmandic/face-api/master/model/';
        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]);
            modelsLoaded = true;
            console.log("Modelos cargados correctamente");
        } catch (error) {
            console.error("Error cargando modelos:", error);
            statusMsg.innerText = "Error cargando sistema de visión. Intente de nuevo.";
            statusMsg.style.color = "#ef4444";
        }
    }

    btnStart.addEventListener('click', async () => {
        const cedula = cedulaInput.value.trim();
        if (!cedula) {
            alert("Por favor ingrese su cédula.");
            return;
        }

        identificationStep.style.display = 'none';
        verificationStep.style.display = 'block';

        if (!modelsLoaded) {
            await loadModels();
        }

        try {
            // 1. Buscar usuario por cédula y obtener su foto_facial
            const response = await fetch('<?php echo URLROOT; ?>/auth/get_user_facial/' + cedula);
            const user = await response.json();

            if (!user || !user.foto_facial) {
                throw new Error("Usuario no encontrado o no tiene rostro enrolado.");
            }

            statusMsg.innerText = "Preparando cámara...";

            // 2. Preparar el descriptor facial del usuario desde su foto guardada
            const img = await faceapi.fetchImage(user.foto_facial);
            const detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();

            if (!detections) {
                throw new Error("No se pudo detectar un rostro en la foto de referencia. Vuelva a enrolar al usuario.");
            }

            labeledDescriptors = new faceapi.LabeledFaceDescriptors(user.nombre, [detections.descriptor]);
            faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);

            // 3. Iniciar Webcam
            stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = stream;

            video.onloadedmetadata = () => {
                loadingModels.style.display = 'none';
                webcamContainer.style.display = 'block';
                statusMsg.innerText = "Escaneando rostro... Manténgase frente a la cámara.";
                statusMsg.style.color = "#3b82f6";
                startRecognition(user);
            };

        } catch (error) {
            alert(error.message);
            resetUI();
        }
    });

    async function startRecognition(user) {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(canvas, displaySize);

        detectionInterval = setInterval(async () => {
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);

            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            // faceapi.draw.drawDetections(canvas, resizedDetections);

            if (detections.length > 0) {
                const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));

                results.forEach(result => {
                    if (result.label !== 'unknown') {
                        // ¡MATCH ENCONTRADO!
                        clearInterval(detectionInterval);
                        statusMsg.innerText = "¡Rostro Verificado! Redirigiendo...";
                        statusMsg.style.color = "#10b981";

                        setTimeout(() => {
                            finalizarMarcacion(user.id);
                        }, 1000);
                    } else {
                        statusMsg.innerText = "Buscando coincidencia... Posicione su rostro correctamente.";
                        statusMsg.style.color = "#f59e0b";
                    }
                });
            }
        }, 500);
    }

    function finalizarMarcacion(usuarioId) {
        // Crear un formulario temporal para enviar el POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo URLROOT; ?>/auth/validar_facial';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'usuario_id';
        input.value = usuarioId;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    function resetUI() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        if (detectionInterval) {
            clearInterval(detectionInterval);
        }
        identificationStep.style.display = 'block';
        verificationStep.style.display = 'none';
        webcamContainer.style.display = 'none';
        loadingModels.style.display = 'flex';
        statusMsg.innerText = "Esperando identificación...";
        statusMsg.style.color = "#64748b";
    }

    btnCancel.addEventListener('click', resetUI);
</script>

<?php require_once '../views/layouts/auth_footer.php'; ?>