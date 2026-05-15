<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Profesional - Gestiona tus Clientes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        /* Header */
        header { text-align: center; color: white; padding: 60px 20px; }
        header h1 { font-size: 3em; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        header p { font-size: 1.3em; opacity: 0.9; max-width: 600px; margin: 0 auto 30px; }
        
        /* Formulario */
        .lead-form { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-width: 500px; margin: 0 auto; }
        .lead-form h2 { color: #667eea; margin-bottom: 25px; text-align: center; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-group input:focus { outline: none; border-color: #667eea; }
        .btn-submit { width: 100%; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; transition: transform 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); }
        
        /* Características */
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 80px; }
        .feature-card { background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .feature-icon { font-size: 3em; margin-bottom: 15px; }
        .feature-card h3 { color: #667eea; margin-bottom: 15px; }
        
        /* Footer */
        footer { text-align: center; color: white; padding: 40px 20px; margin-top: 80px; opacity: 0.8; }
        
        /* Mensaje éxito */
        .success-message { display: none; background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🚀 Impulsa tu Negocio</h1>
            <p>Gestiona tus leads y clientes de forma profesional con nuestro CRM gratuito</p>
        </header>

        <div class="lead-form">
            <h2>¡Comienza Gratis!</h2>
            <div class="success-message" id="successMessage">✅ ¡Gracias! Nos pondremos en contacto pronto.</div>
            <form id="landingForm">
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="nombre" required placeholder="Ej: Juan Pérez">
                </div>
                <div class="form-group">
                    <label>Email Profesional</label>
                    <input type="email" name="email" required placeholder="juan@empresa.com">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" name="telefono" placeholder="+54 9 11 1234 5678">
                </div>
                <div class="form-group">
                    <label>Empresa</label>
                    <input type="text" name="empresa" placeholder="Nombre de tu empresa">
                </div>
                <button type="submit" class="btn-submit">Solicitar Demo Gratis</button>
            </form>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Dashboard Inteligente</h3>
                <p>Visualiza todas tus métricas importantes en tiempo real</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Gestión Rápida</h3>
                <p>Convierte leads en clientes con un solo clic</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>100% Seguro</h3>
                <p>Tus datos protegidos con la mejor tecnología</p>
            </div>
        </div>

        <footer>
            <p>© 2024 CRM Profesional - Todos los derechos reservados</p>
            <p style="margin-top: 10px;"><a href="dashboard.php" style="color: white; text-decoration: underline;">Acceso Admin</a></p>
        </footer>
    </div>

    <script>
        document.getElementById('landingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const datos = Object.fromEntries(formData);
            
            try {
                const response = await fetch('api.php?accion=agregar_lead', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datos)
                });
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('successMessage').style.display = 'block';
                    this.reset();
                    setTimeout(() => {
                        document.getElementById('successMessage').style.display = 'none';
                    }, 5000);
                } else {
                    alert('Error: ' + result.mensaje);
                }
            } catch (error) {
                alert('Error de conexión. Intente nuevamente.');
            }
        });
    </script>
</body>
</html>
