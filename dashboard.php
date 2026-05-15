<?php
session_start();
require_once 'config.php';

// Dashboard - Panel de Administración Nexus CRM
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: dashboard.php');
    exit;
}

// Login simple
if (!isset($_SESSION['admin']) && isset($_POST['password'])) {
    if ($_POST['password'] === 'admin123') {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nexus CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-indigo-500 w-16 h-16 rounded-xl mx-auto flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Nexus CRM</h1>
            <p class="text-slate-500 mt-2">Acceso Administrativo</p>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Contraseña</label>
                <input type="password" name="password" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all" placeholder="••••••••" required>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-all shadow-lg shadow-indigo-200">
                Ingresar
            </button>
        </form>
        <p class="text-center text-xs text-slate-400 mt-6">Contraseña por defecto: admin123</p>
    </div>
</body>
</html>
<?php
    exit;
}

// Obtener estadísticas
$conn = getDBConnection();
$stats = [
    'total_leads' => 0,
    'nuevos_leads' => 0,
    'total_clientes' => 0,
    'ingresos_totales' => 0
];

try {
    $result = $conn->query("SELECT COUNT(*) as total FROM leads");
    $row = $result->fetch_assoc();
    $stats['total_leads'] = $row['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM leads WHERE estado = 'nuevo'");
    $row = $result->fetch_assoc();
    $stats['nuevos_leads'] = $row['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM clientes");
    $row = $result->fetch_assoc();
    $stats['total_clientes'] = $row['total'];
    
    $result = $conn->query("SELECT SUM(valor_potencial) as total FROM clientes");
    $row = $result->fetch_assoc();
    $stats['ingresos_totales'] = $row['total'] ?? 0;
} catch(Exception $e) {
    // Silencioso en caso de error
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus CRM - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased overflow-hidden">

    <div class="flex h-screen w-full">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col transition-all duration-300">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-indigo-500 p-2 rounded-lg text-white">
                    <i data-lucide="layers" class="w-6 h-6"></i>
                </div>
                <span class="text-xl font-bold text-white tracking-tight">Nexus CRM</span>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1 custom-scrollbar overflow-y-auto">
                <p class="px-2 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Menú Principal</p>
                <a href="#" class="flex items-center gap-3 px-3 py-2 bg-indigo-600 text-white rounded-lg transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="#" onclick="mostrarTab('leads')" class="flex items-center gap-3 px-3 py-2 hover:bg-slate-800 hover:text-white rounded-lg transition-all group">
                    <i data-lucide="users" class="w-5 h-5 text-slate-500 group-hover:text-indigo-400"></i>
                    <span class="font-medium">Leads</span>
                </a>
                <a href="#" onclick="mostrarTab('clientes')" class="flex items-center gap-3 px-3 py-2 hover:bg-slate-800 hover:text-white rounded-lg transition-all group">
                    <i data-lucide="briefcase" class="w-5 h-5 text-slate-500 group-hover:text-indigo-400"></i>
                    <span class="font-medium">Clientes</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800 space-y-4">
                <div class="flex items-center gap-3 px-3">
                    <div class="h-10 w-10 rounded-full bg-slate-700 flex items-center justify-center text-sm font-bold text-white border border-slate-600">
                        AD
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-white">Administrador</span>
                        <span class="text-xs text-slate-500">Admin</span>
                    </div>
                </div>
                <a href="?logout=1" class="w-full flex items-center gap-3 px-3 py-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="font-medium">Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10">
                <div class="flex items-center gap-4 w-1/2">
                    <div class="relative w-full max-w-md">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" id="searchInput" placeholder="Buscar leads, clientes..." class="w-full bg-slate-100 border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="abrirModalLead()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm shadow-indigo-200 transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Nuevo Lead
                    </button>
                </div>
            </header>

            <!-- Dashboard View -->
            <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                
                <div class="flex items-end justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Dashboard</h1>
                        <p class="text-slate-500 mt-1">Bienvenido de nuevo. Aquí tienes un resumen de hoy.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                                <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Ingresos Totales</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">$<?php echo number_format($stats['ingresos_totales'], 2); ?></h3>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <i data-lucide="user-plus" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Leads</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1"><?php echo $stats['total_leads']; ?></h3>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                                <i data-lucide="target" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Nuevos Leads</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1"><?php echo $stats['nuevos_leads']; ?></h3>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                                <i data-lucide="users" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Clientes</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1"><?php echo $stats['total_clientes']; ?></h3>
                    </div>
                </div>

                <!-- Main Grid Sections -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Leads Table -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Leads Recientes</h2>
                                <p class="text-xs text-slate-500 uppercase font-semibold mt-1">Gestión de prospectos</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Contacto</th>
                                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Empresa</th>
                                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Estado</th>
                                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100" id="leadsTable">
                                    <!-- Se carga dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Activity Feed -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-slate-800 tracking-tight">Accesos Rápidos</h2>
                        </div>

                        <div class="space-y-4 flex-1">
                            <button onclick="abrirModalLead()" class="w-full flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200">
                                    <i data-lucide="user-plus" class="w-5 h-5 text-indigo-600"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-slate-800">Agregar Lead</p>
                                    <p class="text-xs text-slate-500">Nuevo prospecto</p>
                                </div>
                            </button>

                            <a href="index.php" target="_blank" class="w-full flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                                <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200">
                                    <i data-lucide="external-link" class="w-5 h-5 text-emerald-600"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-slate-800">Ver Landing Page</p>
                                    <p class="text-xs text-slate-500">Página pública</p>
                                </div>
                            </a>

                            <button onclick="cargarDatos()" class="w-full flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-all group">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200">
                                    <i data-lucide="refresh-cw" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-slate-800">Actualizar Datos</p>
                                    <p class="text-xs text-slate-500">Refrescar vista</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Lead -->
    <div class="modal hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50" id="leadModal">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800" id="modalTitle">Nuevo Lead</h2>
                <button onclick="cerrarModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="leadForm">
                <input type="hidden" id="leadId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre</label>
                        <input type="text" id="leadNombre" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" id="leadEmail" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Teléfono</label>
                        <input type="tel" id="leadTelefono" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Empresa</label>
                        <input type="text" id="leadEmpresa" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Estado</label>
                        <select id="leadEstado" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="nuevo">Nuevo</option>
                            <option value="contactado">Contactado</option>
                            <option value="calificado">Calificado</option>
                            <option value="perdido">Perdido</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="cerrarModal()" class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 font-semibold">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Convertir -->
    <div class="modal hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50" id="convertModal">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800">Convertir a Cliente</h2>
                <button onclick="cerrarConvertModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="convertForm">
                <input type="hidden" id="convertLeadId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Valor Potencial ($)</label>
                        <input type="number" id="valorPotencial" step="0.01" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Notas</label>
                        <textarea id="notasCliente" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="cerrarConvertModal()" class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 font-semibold">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold">Convertir</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let leadsData = [];
        let clientesData = [];

        async function cargarDatos() {
            try {
                const [leadsRes, clientesRes] = await Promise.all([
                    fetch('api.php?accion=obtener_leads'),
                    fetch('api.php?accion=obtener_clientes')
                ]);
                
                const leads = await leadsRes.json();
                const clientes = await clientesRes.json();
                
                leadsData = leads.data || [];
                clientesData = clientes.data || [];
                
                renderLeads();
                renderClientes();
            } catch (error) {
                console.error('Error cargando datos:', error);
            }
        }

        function renderLeads() {
            const tbody = document.getElementById('leadsTable');
            if (leadsData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">No hay leads registrados</td></tr>';
                return;
            }
            
            const estadoColors = {
                'nuevo': 'bg-blue-100 text-blue-700',
                'contactado': 'bg-amber-100 text-amber-700',
                'calificado': 'bg-emerald-100 text-emerald-700',
                'perdido': 'bg-red-100 text-red-700'
            };
            
            tbody.innerHTML = leadsData.map(lead => `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 border border-slate-200">${lead.nombre.charAt(0).toUpperCase()}</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">${lead.nombre}</p>
                                <p class="text-xs text-slate-500 leading-tight">${lead.email}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 font-medium tracking-tight">${lead.empresa || '-'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase ${estadoColors[lead.estado] || 'bg-slate-100 text-slate-700'}">${lead.estado}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editarLead(${lead.id})" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <button onclick="abrirConvertModal(${lead.id})" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </button>
                            <button onclick="eliminarLead(${lead.id})" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            
            lucide.createIcons();
        }

        function renderClientes() {
            // Implementación similar si se requiere vista de clientes
        }

        function mostrarTab(tab) {
            // Navegación entre pestañas (se puede expandir)
            console.log('Mostrando:', tab);
        }

        function abrirModalLead() {
            document.getElementById('modalTitle').textContent = 'Nuevo Lead';
            document.getElementById('leadForm').reset();
            document.getElementById('leadId').value = '';
            document.getElementById('leadModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function cerrarModal() {
            document.getElementById('leadModal').classList.add('hidden');
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
                document.getElementById('leadModal').classList.remove('hidden');
                lucide.createIcons();
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
            document.getElementById('convertModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function cerrarConvertModal() {
            document.getElementById('convertModal').classList.add('hidden');
            document.getElementById('convertForm').reset();
        }

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

        // Búsqueda
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const filtered = leadsData.filter(lead => 
                lead.nombre.toLowerCase().includes(term) || 
                lead.email.toLowerCase().includes(term) ||
                (lead.empresa && lead.empresa.toLowerCase().includes(term))
            );
            
            const tbody = document.getElementById('leadsTable');
            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">No se encontraron resultados</td></tr>';
                return;
            }
            
            // Reutilizar lógica de renderizado con datos filtrados
            const originalData = leadsData;
            leadsData = filtered;
            renderLeads();
            leadsData = originalData;
        });

        lucide.createIcons();
        cargarDatos();
    </script>
</body>
</html>
