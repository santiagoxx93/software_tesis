<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Cita</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 35px 20px;
            text-align: center;
        }
        .logo-text {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .sub-logo {
            color: #d1fae5;
            font-size: 14px;
            margin-top: 5px;
        }
        .content {
            padding: 40px 35px;
        }
        h3 {
            color: #111827;
            font-size: 20px;
            margin-top: 0;
        }
        .success-badge {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .details-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .detail-row {
            margin-bottom: 12px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 12px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-label {
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }
        .detail-value {
            color: #0f172a;
            font-weight: 600;
            font-size: 16px;
        }
        .footer {
            background-color: #f8fafc;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="logo-text">Centro San Alfonso</h1>
            <div class="sub-logo">Citas Médicas e Historias Clínicas</div>
        </div>
        
        <div class="content">
            <div class="success-badge">✓ CITA CONFIRMADA</div>
            
            <h3>Hola, {{ ucfirst($cita->paciente->nombres) }}</h3>
            <p>Es un placer informarle que su cita médica ha sido registrada exitosamente en nuestro sistema. A continuación, le detallamos la información de su consulta:</p>
            
            <div class="details-card">
                <div class="detail-row">
                    <span class="detail-label">Fecha de la Cita</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Hora</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Especialista Asignado</span>
                    <span class="detail-value">Dr(a). {{ $cita->especialista->nombres }} {{ $cita->especialista->apellidos }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Especialidad</span>
                    <span class="detail-value">{{ $cita->especialista->especialidad->nombre ?? 'Medicina General' }}</span>
                </div>
            </div>
            
            <p style="color: #ef4444; font-size: 14px; font-weight: 500;">🔔 Por favor, recuerde llegar con al menos 15 minutos de anticipación.</p>
            <p style="font-size: 14px;">Si necesita cancelar o reprogramar, por favor contáctenos a la brevedad posible.</p>
            
            <p style="margin-top: 30px;">Saludos cordiales,<br><strong>El equipo del Centro San Alfonso</strong></p>
        </div>
        
        <div class="footer">
            © {{ date('Y') }} Centro Integral San Alfonso. Todos los derechos reservados.<br>
            Este es un mensaje automático, por favor no responda a este correo.
        </div>
    </div>
</body>
</html>
