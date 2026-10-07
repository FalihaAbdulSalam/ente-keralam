import { useState } from "react";
import DashboardLayout from "./DashboardLayout";
import dashboardAPI from "../services/dashboardAPI";
import { useAuth } from "./App";

export default function UpdatePicture() {
    const { user, setUser } = useAuth();
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');
    const [preview, setPreview] = useState(null);
    const [selectedFile, setSelectedFile] = useState(null);

    const handleFileSelect = (e) => {
        const file = e.target.files[0];
        
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            setError('Please select an image file');
            return;
        }

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            setError('Image size should be less than 2MB');
            return;
        }

        setSelectedFile(file);
        setError('');

        // Create preview
        const reader = new FileReader();
        reader.onloadend = () => {
            setPreview(reader.result);
        };
        reader.readAsDataURL(file);
    };

    const handleUpload = async (e) => {
        e.preventDefault();
        
        if (!selectedFile) {
            setError('Please select an image first');
            return;
        }

        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const formData = new FormData();
            formData.append('avatar', selectedFile);

            const response = await dashboardAPI.updateProfilePicture(formData);
            
            setSuccess('Profile picture updated successfully! ' + 
                (response.data.points_earned ? `🎉 You earned ${response.data.points_earned} points!` : ''));
            
            // Update user in context with new avatar
            if (setUser && response.data.user) {
                setUser(response.data.user);
                // Also update localStorage to persist on refresh
                localStorage.setItem('user', JSON.stringify(response.data.user));
            }

            // Clear preview after 2 seconds
            setTimeout(() => {
                setPreview(null);
                setSelectedFile(null);
                setSuccess('');
            }, 2000);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to upload profile picture');
        } finally {
            setLoading(false);
        }
    };

    const handleRemove = async () => {
        if (!window.confirm('Are you sure you want to remove your profile picture?')) {
            return;
        }

        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const response = await dashboardAPI.removeProfilePicture();
            setSuccess('Profile picture removed successfully!');
            
            // Update user in context
            if (setUser && response.data.user) {
                setUser(response.data.user);
                // Also update localStorage to persist on refresh
                localStorage.setItem('user', JSON.stringify(response.data.user));
            }

            setPreview(null);
            setSelectedFile(null);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to remove profile picture');
        } finally {
            setLoading(false);
        }
    };

    return (
        <DashboardLayout>
            <style>{`
                .update-picture-wrapper {
                    background: rgba(255, 255, 255, 0.9);
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .update-card {
                    max-width: 600px;
                    margin: 0 auto;
                }

                .update-card h3 {
                    margin-bottom: 20px;
                    color: #333;
                }

                .current-picture {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    background: #f8f9fa;
                    padding: 30px;
                    border-radius: 8px;
                    margin-bottom: 25px;
                }

                .current-picture label {
                    display: block;
                    font-size: 14px;
                    color: #666;
                    margin-bottom: 15px;
                }

                .picture-preview {
                    width: 150px;
                    height: 150px;
                    border-radius: 50%;
                    object-fit: cover;
                    margin-bottom: 15px;
                }

                .upload-section {
                    background: #f8f9fa;
                    padding: 30px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    border: 2px dashed #ddd;
                    text-align: center;
                    transition: all 0.3s ease;
                }

                .upload-section:hover {
                    border-color: #4b7bec;
                    background: #f0f4ff;
                }

                .upload-section.has-file {
                    border-color: #27ae60;
                    background: #f0fff4;
                }

                .file-input {
                    display: none;
                }

                .upload-label {
                    display: inline-block;
                    padding: 12px 30px;
                    background: #039;
                    color: white;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background 0.2s;
                    font-weight: 500;
                }

                .upload-label:hover {
                    background: #11306f;
                }

                .upload-info {
                    margin-top: 15px;
                    font-size: 13px;
                    color: #666;
                }

                .file-name {
                    margin-top: 10px;
                    font-weight: 600;
                    color: #27ae60;
                }

                .preview-section {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    margin: 20px 0;
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
                    margin-bottom: 10px;
                }

                .btn-primary:hover {
                    background: #11306f;
                }

                .btn-primary:disabled {
                    background: #95a5a6;
                    cursor: not-allowed;
                }

                .btn-danger {
                    background: #e74c3c;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background 0.2s;
                    width: 100%;
                }

                .btn-danger:hover {
                    background: #c0392b;
                }

                .btn-danger:disabled {
                    background: #95a5a6;
                    cursor: not-allowed;
                }

                .alert {
                    padding: 12px 15px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                }

                .alert-success {
                    background: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .alert-danger {
                    background: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .requirements {
                    background: #fff3cd;
                    border: 1px solid #ffeeba;
                    padding: 15px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                }

                .requirements h4 {
                    font-size: 14px;
                    margin-bottom: 10px;
                    color: #856404;
                }

                .requirements ul {
                    margin: 0;
                    padding-left: 20px;
                    font-size: 13px;
                    color: #856404;
                }

                .requirements li {
                    margin-bottom: 5px;
                }

                @media (max-width: 768px) {
                    .update-picture-wrapper {
                        padding: 20px;
                    }
                    
                    .update-card {
                        padding: 0;
                    }

                    .picture-preview {
                        width: 120px;
                        height: 120px;
                    }
                }
            `}</style>

            <div className="update-picture-wrapper">
                <div className="update-card">
                    <h3>Update Profile Picture</h3>

                    {/* Current Picture */}
                    <div className="current-picture">
                        <label>Current Profile Picture</label>
                        {user?.avatar_url || user?.avatar ? (
                            <img 
                                src={user.avatar_url || user.avatar} 
                                alt="Current profile" 
                                className="picture-preview"
                            />
                        ) : (
                            <div className="picture-preview" style={{
                                background: '#e0e0e0',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                fontSize: '48px',
                                fontWeight: 'bold',
                                color: '#666'
                            }}>
                                {user?.name?.charAt(0).toUpperCase() || 'U'}
                            </div>
                        )}
                        <p style={{ fontSize: '13px', color: '#666', marginTop: '10px' }}>
                            {user?.avatar_url || user?.avatar ? 'You have a profile picture' : 'No profile picture set'}
                        </p>
                    </div>

                    {/* Requirements */}
                    <div className="requirements">
                        <h4>📋 Image Requirements:</h4>
                        <ul>
                            <li>File format: JPG, PNG, or GIF</li>
                            <li>Maximum file size: 2MB</li>
                            <li>Recommended: Square image (1:1 ratio)</li>
                            <li>Minimum dimensions: 200x200 pixels</li>
                        </ul>
                    </div>

                    {success && <div className="alert alert-success">{success}</div>}
                    {error && <div className="alert alert-danger">{error}</div>}

                    {/* Upload Section */}
                    <form onSubmit={handleUpload}>
                        <div className={`upload-section ${selectedFile ? 'has-file' : ''}`}>
                            <input
                                type="file"
                                id="avatar-upload"
                                className="file-input"
                                accept="image/*"
                                onChange={handleFileSelect}
                                disabled={loading}
                            />
                            <label htmlFor="avatar-upload" className="upload-label">
                                {selectedFile ? 'Choose Different Image' : 'Choose Image'}
                            </label>
                            <div className="upload-info">
                                {selectedFile ? (
                                    <div className="file-name">✓ {selectedFile.name}</div>
                                ) : (
                                    <div>Click to select an image from your device</div>
                                )}
                            </div>
                        </div>

                        {/* Preview */}
                        {preview && (
                            <div className="preview-section">
                                <label style={{ fontSize: '14px', color: '#666', marginBottom: '10px' }}>
                                    Preview:
                                </label>
                                <img 
                                    src={preview} 
                                    alt="Preview" 
                                    className="picture-preview"
                                />
                            </div>
                        )}

                        <button 
                            type="submit" 
                            className="btn-primary" 
                            disabled={loading || !selectedFile}
                        >
                            {loading ? 'Uploading...' : 'Upload Picture'}
                        </button>
                    </form>

                    {/* Remove Picture Button */}
                    {(user?.avatar_url || user?.avatar) && (
                        <button 
                            type="button" 
                            className="btn-danger" 
                            onClick={handleRemove}
                            disabled={loading}
                        >
                            {loading ? 'Removing...' : 'Remove Profile Picture'}
                        </button>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
