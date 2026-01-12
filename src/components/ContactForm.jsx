import React, { useState } from 'react';
import { Send } from 'lucide-react';

const ContactForm = () => {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        message: ''
    });
    const [loading, setLoading] = useState(false);
    const [statusMessage, setStatusMessage] = useState('');

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!formData.name || !formData.email || !formData.message) {
            setStatusMessage('Por favor completa todos los campos');
            setTimeout(() => setStatusMessage(''), 3000);
            return;
        }

        setLoading(true);
        setStatusMessage('');

        try {
            const response = await fetch('/api/send-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    to: 'contactus@xlerion.com',
                    from: formData.email,
                    name: formData.name,
                    subject: `Nuevo mensaje de contacto de ${formData.name}`,
                    message: formData.message
                })
            }); if (response.ok) {
                setStatusMessage('¡Mensaje enviado exitosamente! Nos pondremos en contacto pronto.');
                setFormData({ name: '', email: '', message: '' });
                setTimeout(() => setStatusMessage(''), 5000);
            } else {
                setStatusMessage('Error al enviar el mensaje. Intenta nuevamente.');
                setTimeout(() => setStatusMessage(''), 3000);
            }
        } catch (error) {
            console.error('Error:', error);
            setStatusMessage('Error de conexión. Intenta nuevamente.');
            setTimeout(() => setStatusMessage(''), 3000);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="space-y-6">
            <p className="text-sm text-gray-300 leading-relaxed">¿Quieres colaborar, invertir o conocer más sobre Xlerion? Estamos listos para conversar. Completa el formulario o usa los canales directos.</p>
            <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input
                        type="text"
                        name="name"
                        placeholder="Nombre"
                        value={formData.name}
                        onChange={handleChange}
                        className="xl-input"
                        required
                    />
                    <input
                        type="email"
                        name="email"
                        placeholder="Correo electrónico"
                        value={formData.email}
                        onChange={handleChange}
                        className="xl-input"
                        required
                    />
                </div>
                <textarea
                    name="message"
                    placeholder="Mensaje"
                    rows="6"
                    value={formData.message}
                    onChange={handleChange}
                    className="xl-input"
                    required
                ></textarea>
                <button
                    type="submit"
                    disabled={loading}
                    className="xl-btn-primary w-full flex justify-center gap-4 disabled:opacity-50"
                >
                    {loading ? 'Enviando...' : 'Enviar mensaje'} <Send size={18} />
                </button>
            </form>
            {statusMessage && (
                <div className={`text-sm p-3 rounded ${statusMessage.includes('exitosamente') ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400'}`}>
                    {statusMessage}
                </div>
            )}
        </div>
    );
};

export default ContactForm;
