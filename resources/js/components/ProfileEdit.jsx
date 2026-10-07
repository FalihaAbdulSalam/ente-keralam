import { useState, useEffect } from "react";
import DashboardLayout from "./DashboardLayout";
import dashboardAPI from "../services/dashboardAPI";
import { useAuth } from "./App";
import DateOfBirthPicker from "./DateOfBirthPicker";

export default function ProfileEdit() {
    const { user, setUser } = useAuth();
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');
    const [districts, setDistricts] = useState([]);
    const [formData, setFormData] = useState({
        name: '',
        dob: '',
        gender: '',
        district: '',
        address: '',
        pincode: ''
    });
    const handleDobChange = (dobStr) => {
        setFormData(prev => ({ ...prev, dob: dobStr }));
    };

    // Fetch districts on component mount
    useEffect(() => {
        const fetchDistricts = async () => {
            try {
                const response = await dashboardAPI.getDistricts();
                if (response.data.success) {
                    setDistricts(response.data.districts);
                }
            } catch (err) {
                console.error('Failed to fetch districts:', err);
            }
        };
        fetchDistricts();
    }, []);

    useEffect(() => {
        if (user) {
            setFormData({
                name: user.name || '',
                dob: user.dob || '',
                gender: user.gender || '',
                district: user.district || '',
                address: user.address || '',
                pincode: user.pincode || ''
            });
        }
    }, [user]);

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const response = await dashboardAPI.updateProfile(formData);
            setSuccess('Profile updated successfully! ' + 
                (response.data.is_complete && !user.is_profile_complete ? 
                    '🎉 You earned 10 points for completing your profile!' : ''));
            
            // Update user in context
            if (setUser) {
                setUser(response.data.user);
                // Also update localStorage to persist on refresh
                localStorage.setItem('user', JSON.stringify(response.data.user));
            }

            // Reload the page to update sidebar completion percentage
            window.location.reload();
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to update profile');
        } finally {
            setLoading(false);
        }
    };

    return (
        <DashboardLayout>
            <style>{`
                .profile-edit-card {
                    background: rgba(255, 255, 255, 0.85);
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                }

                .profile-edit-card h3 {
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

                .form-control {
                    width: 100%;
                    padding: 10px 15px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                }

                .form-control:focus {
                    outline: none;
                    border-color: #4b7bec;
                }

                .form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
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
                }

                .btn-primary:hover {
                    background: #11306f;
                }

                .btn-primary:disabled {
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

                @media (max-width: 768px) {
                    .form-row {
                        grid-template-columns: 1fr;
                    }

                    .profile-edit-card {
                        padding: 20px;
                    }
                }
            `}</style>

            <div className="profile-edit-card">
                <h3>Edit Profile</h3>

                {success && <div className="alert alert-success">{success}</div>}
                {error && <div className="alert alert-danger">{error}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="form-group">
                        <label>Full Name *</label>
                        <input
                            type="text"
                            name="name"
                            className="form-control"
                            value={formData.name}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    <div className="form-row">
                        <div className="form-group">
                            <label>Date of Birth</label>
                            <DateOfBirthPicker
                                id="edit-profile-dob"
                                name="dob"
                                value={formData.dob}
                                onChange={handleDobChange}
                                inputClassName="form-control"
                                wrapperClassName="w-100"
                            />
                        </div>

                        <div className="form-group">
                            <label>Gender</label>
                            <select
                                name="gender"
                                className="form-control"
                                value={formData.gender}
                                onChange={handleChange}
                            >
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div className="form-group">
                        <label>Address</label>
                        <textarea
                            name="address"
                            className="form-control"
                            rows="3"
                            value={formData.address}
                            onChange={handleChange}
                            placeholder="Enter your full address"
                        ></textarea>
                    </div>

                    <div className="form-row">
                        <div className="form-group">
                            <label>Pincode</label>
                            <input
                                type="text"
                                name="pincode"
                                className="form-control"
                                value={formData.pincode}
                                onChange={handleChange}
                                placeholder="6-digit pincode"
                                maxLength="6"
                            />
                        </div>

                        <div className="form-group">
                            <label>District</label>
                            <select
                                name="district"
                                className="form-control"
                                value={formData.district}
                                onChange={handleChange}
                            >
                                <option value="">Select District</option>
                                {districts.map((district) => (
                                    <option key={district.id} value={district.id}>
                                        {district.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div style={{ marginTop: '30px' }}>
                        <button type="submit" className="btn-primary" disabled={loading}>
                            {loading ? 'Updating...' : 'Update Profile'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
