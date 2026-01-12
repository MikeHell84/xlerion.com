import express from 'express';
import nodemailer from 'nodemailer';
import cors from 'cors';
import bodyParser from 'body-parser';

const app = express();
const PORT = 3001;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Configurar transporter de nodemailer
const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
        user: process.env.EMAIL_USER || 'your-email@gmail.com',
        pass: process.env.EMAIL_PASSWORD || 'your-app-password'
    }
});

// Endpoint para enviar emails
app.post('/api/send-email', async (req, res) => {
    const { to, from, name, subject, message } = req.body;

    // Validar datos
    if (!to || !from || !name || !subject || !message) {
        return res.status(400).json({ error: 'Faltan campos requeridos' });
    }

    try {
        const mailOptions = {
            from: process.env.EMAIL_USER || 'noreply@xlerion.com',
            to: to,
            subject: subject,
            html: `
        <h2>${subject}</h2>
        <p><strong>De:</strong> ${name} (${from})</p>
        <p><strong>Mensaje:</strong></p>
        <p>${message.replace(/\n/g, '<br>')}</p>
      `
        };

        await transporter.sendMail(mailOptions);
        res.json({ success: true, message: 'Email enviado correctamente' });
    } catch (error) {
        console.error('Error al enviar email:', error);
        res.status(500).json({ error: 'Error al enviar el email' });
    }
});

app.listen(PORT, () => {
    console.log(`Servidor de emails escuchando en puerto ${PORT}`);
});
