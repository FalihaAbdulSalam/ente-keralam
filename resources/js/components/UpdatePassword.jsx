import { useState } from 'react';
import DashboardLayout from './DashboardLayout';
import dashboardAPI from '../services/dashboardAPI';

export default function UpdatePassword() {
    const [formData, setFormData] = useState({
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState(false);
    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        // Clear error for this field
        if (errors[name]) {
            setErrors(prev => ({ ...prev, [name]: null }));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        // Client-side validation
        const newErrors = {};
        if (!formData.current_password) {
            newErrors.current_password = 'Current password is required';
        }
        if (!formData.new_password) {
            newErrors.new_password = 'New password is required';
        } else if (formData.new_password.length < 8) {
            newErrors.new_password = 'Password must be at least 8 characters';
        }
        if (formData.new_password !== formData.new_password_confirmation) {
            newErrors.new_password_confirmation = 'Passwords do not match';
        }

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        setLoading(true);
        setError('');

        try {
            const response = await dashboardAPI.updatePassword({
                current_password: formData.current_password,
                new_password: formData.new_password,
                new_password_confirmation: formData.new_password_confirmation
            });

            setSuccess(true);
            setFormData({
                current_password: '',
                new_password: '',
                new_password_confirmation: ''
            });
            
            setTimeout(() => {
                setSuccess(false);
            }, 5000);
        } catch (err) {
            if (err.response?.data?.errors) {
                setErrors(err.response.data.errors);
            }
            setError(err.response?.data?.message || 'Failed to update password');
        } finally {
            setLoading(false);
        }
    };

    return (
        <DashboardLayout>
            <style>{`
                .update-password-wrapper {
                    background: rgba(255, 255, 255, 0.9);
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .update-password-container {
                    max-width: 500px;
                    margin: 0 auto;
                }

                .update-password-header h3 {
                    margin-bottom: 20px;
                    color: #333;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    color: #555;
                    font-weight: 500;
                }

                .form-group input {
                    width: 100%;
                    padding: 12px 15px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                }

                .form-group input:focus {
                    outline: none;
                    border-color: #4b7bec;
                }

                .form-group input.error {
                    border-color: #e74c3c;
                }

                .error-message {
                    color: #e74c3c;
                    font-size: 13px;
                    margin-top: 5px;
                }

                .password-requirements {
                    background: #f8f9fa;
                    border-left: 4px solid #4b7bec;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 25px;
                }

                .password-requirements h4 {
                    font-size: 14px;
                    color: #333;
                    margin-bottom: 10px;
                    font-weight: 600;
                }

                .password-requirements ul {
                    margin: 0;
                    padding-left: 20px;
                }

                .password-requirements li {
                    font-size: 13px;
                    color: #666;
                    margin-bottom: 5px;
                }

                .btn-primary {
                    background: #039;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background 0.2s;
                    width: 100%;
                }

                .btn-primary:hover {
                    background: #11306f;
                }

                .btn-primary:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                }

                .alert {
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    font-size: 14px;
                }

                .alert-success {
                    background: #d4edda;
                    color: #155724;
                    border-left: 4px solid #28a745;
                }

                .alert-danger {
                    background: #f8d7da;
                    color: #721c24;
                    border-left: 4px solid #dc3545;
                }
            `}</style>

            <div className="update-password-wrapper">
                <div className="update-password-container">
                    <div className="update-password-header">
                        <h3>Update Password</h3>
                    </div>

                    {success && (
                        <div className="alert alert-success">
                            ✓ Password updated successfully!
                        </div>
                    )}

                    {error && (
                        <div className="alert alert-danger">
                            {error}
                        </div>
                    )}

                    <div className="password-requirements">
                        <h4>Password Requirements:</h4>
                        <ul>
                            <li>Minimum 8 characters long</li>
                            <li>Should contain uppercase and lowercase letters</li>
                            <li>Should contain at least one number</li>
                            <li>Should contain at least one special character</li>
                        </ul>
                    </div>

                    <form onSubmit={handleSubmit}>
                        <div className="form-group">
                            <label htmlFor="current_password">Current Password *</label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                value={formData.current_password}
                                onChange={handleChange}
                                className={errors.current_password ? 'error' : ''}
                                placeholder="Enter current password"
                            />
                            {errors.current_password && (
                                <div className="error-message">{errors.current_password}</div>
                            )}
                        </div>

                        <div className="form-group">
                            <label htmlFor="new_password">New Password *</label>
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                value={formData.new_password}
                                onChange={handleChange}
                                className={errors.new_password ? 'error' : ''}
                                placeholder="Enter new password"
                            />
                            {errors.new_password && (
                                <div className="error-message">{errors.new_password}</div>
                            )}
                        </div>

                        <div className="form-group">
                            <label htmlFor="new_password_confirmation">Confirm New Password *</label>
                            <input
                                type="password"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                value={formData.new_password_confirmation}
                                onChange={handleChange}
                                className={errors.new_password_confirmation ? 'error' : ''}
                                placeholder="Re-enter new password"
                            />
                            {errors.new_password_confirmation && (
                                <div className="error-message">{errors.new_password_confirmation}</div>
                            )}
                        </div>

                        <button 
                            type="submit" 
                            className="btn-primary"
                            disabled={loading}
                        >
                            {loading ? 'Updating...' : 'Update Password'}
                        </button>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
