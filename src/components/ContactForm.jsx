import React, { useState } from 'react';
import { Send } from 'lucide-react';
import { useLanguage } from '../context/LanguageContext';

const ContactForm = () => {
    const { t } = useLanguage();
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        message: ''
    });
    const [loading, setLoading] = useState(false);
    const [statusMessage, setStatusMessage] = useState('');
    const [isSuccess, setIsSuccess] = useState(false);

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
            setStatusMessage(t('contact_form_error_fields'));
            setTimeout(() => setStatusMessage(''), 3000);
            return;
        }

        setLoading(true);
        setStatusMessage('');

        // Detectar si estamos en desarrollo local o producción
        const isDevelopment = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        const apiUrl = isDevelopment
            ? 'http://localhost:3001/api/send-email'  // Servidor Node.js local
            : '/api/send-email.php';  // PHP en producción

        console.log('Enviando a:', apiUrl);

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.name,
                    email: formData.email,
                    message: formData.message
                })
            });

            const data = await response.json();

            if (response.ok) {
                setIsSuccess(true);
                setStatusMessage(t('contact_form_success'));
                setFormData({ name: '', email: '', message: '' });
                setTimeout(() => setStatusMessage(''), 5000);
            } else {
                setIsSuccess(false);
                console.error('Error del servidor:', data);
                setStatusMessage(t('contact_form_error_send'));
                setTimeout(() => setStatusMessage(''), 3000);
            }
        } catch (error) {
            setIsSuccess(false);
            console.error('Error:', error);
            setStatusMessage(t('contact_form_error_connection'));
            setTimeout(() => setStatusMessage(''), 3000);
        } finally {
            setLoading(false);
        }
    }; return (
        <div className="space-y-6">
            <p className="text-sm text-gray-300 leading-relaxed">{t('contact_form_intro')}</p>
            <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input
                        type="text"
                        name="name"
                        placeholder={t('contact_form_name')}
                        value={formData.name}
                        onChange={handleChange}
                        className="xl-input"
                        required
                    />
                    <input
                        type="email"
                        name="email"
                        placeholder={t('contact_form_email')}
                        value={formData.email}
                        onChange={handleChange}
                        className="xl-input"
                        required
                    />
                </div>
                <textarea
                    name="message"
                    placeholder={t('contact_form_message')}
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
                    {loading ? t('contact_form_sending') : t('contact_form_send')} <Send size={18} />
                </button>
            </form>
            {statusMessage && (
                <div className={`text-sm p-3 rounded-lg border ${isSuccess
                        ? 'bg-green-900/20 border-green-500/30 text-green-400'
                        : 'bg-red-900/20 border-red-500/30 text-red-400'
                    }`}>
                    {statusMessage}
                </div>
            )}
        </div>
    );
};

export default ContactForm;
