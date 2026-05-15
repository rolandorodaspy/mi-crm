# 🚀 CRM Pro

Sistema de gestión de clientes (CRM) con landing page integrada.

## Características

- **Landing Page**: Página de aterrizaje moderna y responsive para captar leads
- **Dashboard Completo**: Panel de administración para gestionar leads y clientes
- **Gestión de Leads**: Captura, seguimiento y conversión de leads potenciales
- **Gestión de Clientes**: Cartera de clientes con seguimiento de contratos
- **Base de Datos SQLite**: Almacenamiento local rápido y confiable
- **API REST**: Endpoints para integración con otras aplicaciones

## Estructura del Proyecto

```
crm-pro/
├── db/
│   └── database.js      # Configuración de base de datos
├── public/
│   ├── index.html       # Landing page
│   └── dashboard.html   # Panel de administración
├── routes/
│   └── api.js           # Rutas de la API REST
├── server.js            # Servidor principal Express
└── package.json         # Dependencias del proyecto
```

## Instalación

1. Instalar dependencias:
```bash
npm install
```

2. Iniciar el servidor:
```bash
npm start
```

El servidor se ejecutará en `http://localhost:3000`

## Uso

### Landing Page
- Accede a `http://localhost:3000` para ver la landing page
- El formulario de contacto captura nuevos leads automáticamente

### Dashboard
- Accede a `http://localhost:3000/dashboard` para el panel de administración
- Visualiza estadísticas en tiempo real
- Gestiona leads: crear, editar, eliminar y convertir a clientes
- Consulta la cartera de clientes

## API Endpoints

### Leads
- `GET /api/leads` - Obtener todos los leads
- `POST /api/leads` - Crear nuevo lead
- `PUT /api/leads/:id` - Actualizar lead
- `DELETE /api/leads/:id` - Eliminar lead

### Clientes
- `GET /api/clientes` - Obtener todos los clientes
- `POST /api/clientes/convertir/:leadId` - Convertir lead a cliente

## Tecnologías Utilizadas

- **Backend**: Node.js + Express
- **Base de Datos**: SQLite (better-sqlite3)
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Estilos**: CSS personalizado con diseño responsive

## Licencia

ISC
