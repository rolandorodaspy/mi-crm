<?php
session_start();
// Dashboard - Panel de Administración
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Login simple (puedes cambiar la contraseña)
if (!isset($_SESSION['admin']) && isset($_POST['password'])) {
    if ($_POST['password'] === 'admin123') { // CAMBIAR CONTRASEÑA
        $_SESSION['admin'] = true;
    } else {
        $error = "Contraseña incorrecta";
    }
}

if (!isset($_SESSION['admin'])) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - CRM</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100vh; display: flex; justify-content: center; align-items: center; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #667eea; margin-bottom: 20px; }
        input[type="password"] { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #5568d3; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Acceso Admin</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
        <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #666;">Contraseña por defecto: admin123</p>
    </div>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRM</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f5f6fa; }
        .navbar { background: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 1.5em; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; background: rgba(255,255,255,0.2); border-radius: 5px; }
        .container { max-width: 1400px; margin: 0 auto; padding: 30px; }
        
        /* Stats */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #667eea; }
        .stat-label { color: #666; margin-top: 5px; }
        
        /* Tabs */
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { padding: 12px 25px; background: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
        .tab-btn.active { background: #667eea; color: white; }
        
        /* Tablas */
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; font-weight: 600; }
        tr:hover { background: #f8f9fa; }
        
        /* Botones de acción */
        .btn { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; margin-right: 5px; font-size: 13px; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-convert { background: #28a745; color: white; }
        .btn-add { background: #667eea; color: white; padding: 12px 25px; border-radius: 8px; margin-bottom: 20px; cursor: pointer; border: none; font-size: 14px; }
        
        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .close { font-size: 24px; cursor: pointer; color: #666; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-cancel { background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-save { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        
        .badge { padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; }
        .badge-nuevo { background: #17a2b8; color: white; }
        .badge-contactado { background: #ffc107; color: #333; }
        .badge-calificado { background: #28a745; color: white; }
        .badge-perdido { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📊 CRM Dashboard</h1>
        <div>
            <a href="index.php" target="_blank">Ver Landing</a>
            <a href="?logout=1">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number" id="totalLeads">0</div>
                <div class="stat-label">Total Leads</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="nuevosLeads">0</div>
                <div class="stat-label">Nuevos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalClientes">0</div>
                <div class="stat-label">Clientes</div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="mostrarTab('leads')">Leads</button>
            <button class="tab-btn" onclick="mostrarTab('clientes')">Clientes</button>
        </div>

        <div id="leadsSection">
            <button class="btn-add" onclick="abrirModalLead()">+ Nuevo Lead</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Empresa</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="leadsTable"></tbody>
                </table>
            </div>
        </div>

        <div id="clientesSection" style="display: none;">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Empresa</th>
                            <th>Valor Potencial</th>
                            <th>Notas</th>
                            <th>Fecha Conversión</th>
                        </tr>
                    </thead>
                    <tbody id="clientesTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Lead -->
    <div class="modal" id="leadModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Lead</h2>
                <span class="close" onclick="cerrarModal()">&times;</span>
            </div>
            <form id="leadForm">
                <input type="hidden" id="leadId">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" id="leadNombre" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="leadEmail" required>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" id="leadTelefono">
                </div>
                <div class="form-group">
                    <label>Empresa</label>
                    <input type="text" id="leadEmpresa">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select id="leadEstado">
                        <option value="nuevo">Nuevo</option>
                        <option value="contactado">Contactado</option>
                        <option value="calificado">Calificado</option>
                        <option value="perdido">Perdido</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Convertir -->
    <div class="modal" id="convertModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Convertir a Cliente</h2>
                <span class="close" onclick="cerrarConvertModal()">&times;</span>
            </div>
            <form id="convertForm">
                <input type="hidden" id="convertLeadId">
                <div class="form-group">
                    <label>Valor Potencial ($)</label>
                    <input type="number" id="valorPotencial" step="0.01">
                </div>
                <div class="form-group">
                    <label>Notas</label>
                    <textarea id="notasCliente" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="cerrarConvertModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Convertir</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let leadsData = [];
        let clientesData = [];

        // Cargar datos iniciales
        async function cargarDatos() {
            try {
                const [leadsRes, clientesRes, statsRes] = await Promise.all([
                    fetch('api.php?accion=obtener_leads'),
                    fetch('api.php?accion=obtener_clientes'),
                    fetch('api.php?accion=estadisticas')
                ]);
                
                const leads = await leadsRes.json();
                const clientes = await clientesRes.json();
                const stats = await statsRes.json();
                
                leadsData = leads.data || [];
                clientesData = clientes.data || [];
                
                renderLeads();
                renderClientes();
                
                document.getElementById('totalLeads').textContent = stats.leads || 0;
                document.getElementById('nuevosLeads').textContent = stats.nuevos || 0;
                document.getElementById('totalClientes').textContent = stats.clientes || 0;
            } catch (error) {
                console.error('Error cargando datos:', error);
            }
        }

        function renderLeads() {
            const tbody = document.getElementById('leadsTable');
            tbody.innerHTML = leadsData.map(lead => `
                <tr>
                    <td>${lead.nombre}</td>
                    <td>${lead.email}</td>
                    <td>${lead.telefono || '-'}</td>
                    <td>${lead.empresa || '-'}</td>
                    <td><span class="badge badge-${lead.estado}">${lead.estado}</span></td>
                    <td>
                        <button class="btn btn-edit" onclick="editarLead(${lead.id})">Editar</button>
                        <button class="btn btn-convert" onclick="abrirConvertModal(${lead.id})">Convertir</button>
                        <button class="btn btn-delete" onclick="eliminarLead(${lead.id})">Eliminar</button>
                    </td>
                </tr>
            `).join('');
        }

        function renderClientes() {
            const tbody = document.getElementById('clientesTable');
            tbody.innerHTML = clientesData.map(cliente => `
                <tr>
                    <td>${cliente.nombre}</td>
                    <td>${cliente.email}</td>
                    <td>${cliente.telefono || '-'}</td>
                    <td>${cliente.empresa || '-'}</td>
                    <td>$${cliente.valor_potencial || '0.00'}</td>
                    <td>${cliente.notas || '-'}</td>
                    <td>${new Date(cliente.fecha_conversion).toLocaleDateString()}</td>
                </tr>
            `).join('');
        }

        function mostrarTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('leadsSection').style.display = tab === 'leads' ? 'block' : 'none';
            document.getElementById('clientesSection').style.display = tab === 'clientes' ? 'block' : 'none';
        }

        function abrirModalLead() {
            document.getElementById('modalTitle').textContent = 'Nuevo Lead';
            document.getElementById('leadForm').reset();
            document.getElementById('leadId').value = '';
            document.getElementById('leadModal').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('leadModal').style.display = 'none';
        }

        function editarLead(id) {
            const lead = leadsData.find(l => l.id == id);
            if (lead) {
                document.getElementById('modalTitle').textContent = 'Editar Lead';
                document.getElementById('leadId').value = lead.id;
                document.getElementById('leadNombre').value = lead.nombre;
                document.getElementById('leadEmail').value = lead.email;
                document.getElementById('leadTelefono').value = lead.telefono || '';
                document.getElementById('leadEmpresa').value = lead.empresa || '';
                document.getElementById('leadEstado').value = lead.estado;
                document.getElementById('leadModal').style.display = 'flex';
            }
        }

        function eliminarLead(id) {
            if (confirm('¿Estás seguro de eliminar este lead?')) {
                fetch('api.php?accion=eliminar_lead', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                }).then(() => cargarDatos());
            }
        }

        function abrirConvertModal(id) {
            document.getElementById('convertLeadId').value = id;
            document.getElementById('convertModal').style.display = 'flex';
        }

        function cerrarConvertModal() {
            document.getElementById('convertModal').style.display = 'none';
            document.getElementById('convertForm').reset();
        }

        // Formulario Lead
        document.getElementById('leadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('leadId').value;
            const datos = {
                nombre: document.getElementById('leadNombre').value,
                email: document.getElementById('leadEmail').value,
                telefono: document.getElementById('leadTelefono').value,
                empresa: document.getElementById('leadEmpresa').value,
                estado: document.getElementById('leadEstado').value
            };
            
            if (id) datos.id = id;
            
            const accion = id ? 'editar_lead' : 'agregar_lead';
            
            await fetch(`api.php?accion=${accion}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            
            cerrarModal();
            cargarDatos();
        });

        // Formulario Convertir
        document.getElementById('convertForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const datos = {
                id: document.getElementById('convertLeadId').value,
                valor_potencial: document.getElementById('valorPotencial').value,
                notas: document.getElementById('notasCliente').value
            };
            
            await fetch('api.php?accion=convertir_cliente', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            
            cerrarConvertModal();
            cargarDatos();
        });

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Inicializar
        cargarDatos();
    </script>
</body>
</html>
