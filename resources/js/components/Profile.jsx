"use client";
import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { FaChevronUp, FaBars, FaTimesCircle, FaSearch, FaUser, FaLock, FaPhone, FaEnvelope, FaSave } from "react-icons/fa";
import { useAuth } from './App';
import DateOfBirthPicker from "./DateOfBirthPicker";

export default function ProfilePage() {
    const { user, isAuthenticated, loading } = useAuth();
    const navigate = useNavigate();
    const [activeTab, setActiveTab] = useState('profile');
    const [message, setMessage] = useState('');
    const [submitLoading, setSubmitLoading] = useState(false);
    
    // Profile form data
    const [profileData, setProfileData] = useState({
        first_name: '',
        middle_name: '',
        last_name: '',
        role: '',
        dob: '',
        gender: '',
        email: '',
        mobile: ''
    });
    
    // Password form data
    const [passwordData, setPasswordData] = useState({
        current_password: '',
        new_password: '',
        confirm_password: ''
    });
    const handleDobChange = (dobStr) => {
        setProfileData(prev => ({ ...prev, dob: dobStr }));
    };

    // Redirect if not authenticated
    useEffect(() => {
        if (!loading && !isAuthenticated) {
            navigate('/login');
        }
    }, [isAuthenticated, loading, navigate]);

    // Populate form with user data
    useEffect(() => {
        if (user) {
            // Parse the full name back into components
            const nameParts = user.name ? user.name.split(' ') : ['', '', ''];
            const first_name = nameParts[0] || '';
            const last_name = nameParts[nameParts.length - 1] || '';
            const middle_name = nameParts.length > 2 ? nameParts.slice(1, -1).join(' ') : (nameParts[1] || '');
            
            setProfileData({
                first_name: first_name,
                middle_name: middle_name,
                last_name: last_name,
                role: user.role || '',
                dob: user.dob || '',
                gender: user.gender || '',
                email: user.email || '',
                mobile: user.phone || ''
            });
        }
    }, [user]);

    const handleProfileInputChange = (e) => {
        setProfileData({
            ...profileData,
            [e.target.name]: e.target.value
        });
    };

    const handlePasswordInputChange = (e) => {
        setPasswordData({
            ...passwordData,
            [e.target.name]: e.target.value
        });
    };

    const handleProfileUpdate = async (e) => {
        e.preventDefault();
        setSubmitLoading(true);
        setMessage('');

        try {
            // Combine name fields for backend
            const updateData = {
                name: `${profileData.first_name} ${profileData.middle_name} ${profileData.last_name}`.trim(),
                email: profileData.email,
                phone: profileData.mobile,
                role: profileData.role,
                dob: profileData.dob,
                gender: profileData.gender
            };

            const response = await fetch('/api/user/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
                body: JSON.stringify(updateData),
            });

            const data = await response.json();

            if (data.success) {
                setMessage('Profile updated successfully!');
                // Update user data in localStorage
                const updatedUser = { ...user, ...updateData };
                localStorage.setItem('user', JSON.stringify(updatedUser));
            } else {
                setMessage(data.message || 'Failed to update profile');
            }
        } catch (error) {
            setMessage('Network error occurred');
        }
        setSubmitLoading(false);
    };

    const handlePasswordUpdate = async (e) => {
        e.preventDefault();
        setSubmitLoading(true);
        setMessage('');

        // Validate password confirmation
        if (passwordData.new_password !== passwordData.confirm_password) {
            setMessage('New passwords do not match');
            setSubmitLoading(false);
            return;
        }

        try {
            const response = await fetch('/api/user/password', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
                body: JSON.stringify({
                    current_password: passwordData.current_password,
                    new_password: passwordData.new_password
                }),
            });

            const data = await response.json();

            if (data.success) {
                setMessage('Password updated successfully!');
                setPasswordData({
                    current_password: '',
                    new_password: '',
                    confirm_password: ''
                });
            } else {
                setMessage(data.message || 'Failed to update password');
            }
        } catch (error) {
            setMessage('Network error occurred');
        }
        setSubmitLoading(false);
    };

    if (loading) {
        return <div id="preloader"></div>;
    }

    if (!isAuthenticated) {
        return null; // Will redirect via useEffect
    }

    return (
        <>
            <header id="it-nw-header" className="it-nw-header-area">
                <div className="container-top">
                    {/* Top Bar */}
                    <div className="it-nw-header-top-content d-flex justify-content-between">
                        <div className="it-nw-header-cta-social d-flex">
                            <div className="it-nw-header-cta ul-li">
                                <ul>
                                    <li>
                                        <img src="/design/assets/new/loW.svg" alt="" />
                                        <span className="govt">GOVERNMENT OF KERALA</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div className="it-nw-header-login ul-li d-flex">
                            <ul className="shar">
                                {/* <li><a href="#">Skip to Main content</a></li> */}
                                <li><a href="#"><span>മലയാളം</span></a></li>
                                <li className="dropdown">
                                    <img src="/design/assets/share.png" alt="" width="20" />
                                    <ul className="dropdown-content">
                                        <li><a href="#"><img src="/design/assets/youtube.png" alt="" /></a></li>
                                        <li><a href="#"><img src="/design/assets/facebook.png" alt="" /></a></li>
                                        <li><a href="#"><img src="/design/assets/twitter.png" alt="" /></a></li>
                                        <li><a href="#"><img src="/design/assets/instagram.png" alt="" /></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>             
                </div>
            </header>

            {/* Profile Content */}
            <section className="it-nw-breadcrumb-area it-nw-breadcrumb-bg" style={{
                background: "linear-gradient(90deg, #1ec5fa 0%, #0d47d5 50%, #1ec5fa)",
                paddingTop: "120px",
                paddingBottom: "80px"
            }}>
                <div className="container">
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="it-nw-breadcrumb-content text-center">
                                <h1 className="it-nw-breadcrumb-title text-white">My Profile</h1>
                                <div className="it-nw-breadcrumb-list ul-li text-center">
                                    <nav aria-label="breadcrumb">
                                        <ol className="breadcrumb bg-transparent">
                                            <li className="breadcrumb-item"><Link to="/" className="text-white">Home</Link></li>
                                            <li className="breadcrumb-item active" aria-current="page">Profile</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="it-nw-about-content" style={{ paddingTop: "80px", paddingBottom: "80px" }}>
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-10">
                            <div className="card shadow">
                                <div className="card-body p-5">
                                    {/* Profile Header */}
                                    <div className="text-center mb-4">
                                        <div className="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle" 
                                             style={{ width: "80px", height: "80px", fontSize: "32px" }}>
                                            <FaUser />
                                        </div>
                                        <h3 className="mt-3 mb-1">{user?.name || 'User'}</h3>
                                        <p className="text-muted">{user?.email}</p>
                                    </div>

                                    {/* Message Display */}
                                    {message && (
                                        <div className={`alert ${message.includes('success') ? 'alert-success' : 'alert-danger'} text-center`}>
                                            {message}
                                        </div>
                                    )}

                                    {/* Tab Navigation */}
                                    <ul className="nav nav-tabs justify-content-center mb-4" role="tablist">
                                        <li className="nav-item">
                                            <button 
                                                className={`nav-link ${activeTab === 'profile' ? 'active' : ''}`}
                                                onClick={() => setActiveTab('profile')}
                                            >
                                                <FaUser className="me-2" />
                                                Profile Information
                                            </button>
                                        </li>
                                        <li className="nav-item">
                                            <button 
                                                className={`nav-link ${activeTab === 'password' ? 'active' : ''}`}
                                                onClick={() => setActiveTab('password')}
                                            >
                                                <FaLock className="me-2" />
                                                Change Password
                                            </button>
                                        </li>
                                    </ul>

                                    {/* Tab Content */}
                                    <div className="tab-content">
                                        {/* Profile Information Tab */}
                                        {activeTab === 'profile' && (
                                            <div className="tab-pane fade show active">
                                                <form onSubmit={handleProfileUpdate}>
                                                    <div className="row">
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">First Name <span className="text-danger">*</span></label>
                                                                <input
                                                                    type="text"
                                                                    className="form-control form-control-lg"
                                                                    name="first_name"
                                                                    value={profileData.first_name}
                                                                    onChange={handleProfileInputChange}
                                                                    required
                                                                />
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Middle Name</label>
                                                                <input
                                                                    type="text"
                                                                    className="form-control form-control-lg"
                                                                    name="middle_name"
                                                                    value={profileData.middle_name}
                                                                    onChange={handleProfileInputChange}
                                                                />
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Last Name <span className="text-danger">*</span></label>
                                                                <input
                                                                    type="text"
                                                                    className="form-control form-control-lg"
                                                                    name="last_name"
                                                                    value={profileData.last_name}
                                                                    onChange={handleProfileInputChange}
                                                                    required
                                                                />
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Role <span className="text-danger">*</span></label>
                                                                <div className="mt-2">
                                                                    <div className="form-check">
                                                                        <input
                                                                            type="radio"
                                                                            className="form-check-input"
                                                                            name="role"
                                                                            value="public"
                                                                            checked={profileData.role === 'public'}
                                                                            onChange={handleProfileInputChange}
                                                                            required
                                                                        />
                                                                        <label className="form-check-label">Public</label>
                                                                    </div>
                                                                    <div className="form-check">
                                                                        <input
                                                                            type="radio"
                                                                            className="form-check-input"
                                                                            name="role"
                                                                            value="institute"
                                                                            checked={profileData.role === 'institute'}
                                                                            onChange={handleProfileInputChange}
                                                                            required
                                                                        />
                                                                        <label className="form-check-label">Institutes</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Date of Birth <span className="text-danger">*</span></label>
                                                                <DateOfBirthPicker
                                                                    id="profile-dob"
                                                                    name="dob"
                                                                    value={profileData.dob}
                                                                    onChange={handleDobChange}
                                                                    inputClassName="form-control form-control-lg"
                                                                    wrapperClassName="w-100"
                                                                    required
                                                                />
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Gender <span className="text-danger">*</span></label>
                                                                <div className="mt-2">
                                                                    <div className="form-check">
                                                                        <input
                                                                            type="radio"
                                                                            className="form-check-input"
                                                                            name="gender"
                                                                            value="male"
                                                                            checked={profileData.gender === 'male'}
                                                                            onChange={handleProfileInputChange}
                                                                            required
                                                                        />
                                                                        <label className="form-check-label">Male</label>
                                                                    </div>
                                                                    <div className="form-check">
                                                                        <input
                                                                            type="radio"
                                                                            className="form-check-input"
                                                                            name="gender"
                                                                            value="female"
                                                                            checked={profileData.gender === 'female'}
                                                                            onChange={handleProfileInputChange}
                                                                            required
                                                                        />
                                                                        <label className="form-check-label">Female</label>
                                                                    </div>
                                                                    <div className="form-check">
                                                                        <input
                                                                            type="radio"
                                                                            className="form-check-input"
                                                                            name="gender"
                                                                            value="transgender"
                                                                            checked={profileData.gender === 'transgender'}
                                                                            onChange={handleProfileInputChange}
                                                                            required
                                                                        />
                                                                        <label className="form-check-label">Transgender</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Email Address <span className="text-danger">*</span></label>
                                                                <input
                                                                    type="email"
                                                                    className="form-control form-control-lg"
                                                                    name="email"
                                                                    value={profileData.email}
                                                                    onChange={handleProfileInputChange}
                                                                    required
                                                                />
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Mobile <span className="text-danger">*</span></label>
                                                                <input
                                                                    type="text"
                                                                    className="form-control form-control-lg"
                                                                    name="mobile"
                                                                    value={profileData.mobile}
                                                                    onChange={handleProfileInputChange}
                                                                    maxLength="10"
                                                                    required
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="text-center mt-4">
                                                        <button
                                                            type="submit"
                                                            className="btn btn-lg px-5"
                                                            disabled={submitLoading}
                                                            style={{
                                                                backgroundImage: "linear-gradient(90deg, #1ec5fa 0%, #0d47d5 50%, #1ec5fa)",
                                                                border: "none",
                                                                color: "white"
                                                            }}
                                                        >
                                                            <FaSave className="me-2" />
                                                            {submitLoading ? 'Updating...' : 'Update Profile'}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        )}

                                        {/* Change Password Tab */}
                                        {activeTab === 'password' && (
                                            <div className="tab-pane fade show active">
                                                <form onSubmit={handlePasswordUpdate}>
                                                    <div className="row justify-content-center">
                                                        <div className="col-md-8">
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">Current Password <span className="text-danger">*</span></label>
                                                                <input
                                                                    type="password"
                                                                    className="form-control form-control-lg"
                                                                    name="current_password"
                                                                    value={passwordData.current_password}
                                                                    onChange={handlePasswordInputChange}
                                                                    required
                                                                />
                                                            </div>
                                                            <div className="form-group mb-3">
                                                                <label className="form-label">New Password <span className="text-danger">*</span></label>
                                                                <input
                                                                    type="password"
                                                                    className="form-control form-control-lg"
                                                                    name="new_password"
                                                                    value={passwordData.new_password}
                                                                    onChange={handlePasswordInputChange}
                                                                    minLength="6"
                                                                    required
                                                                />
                                                                <small className="form-text text-muted">Password must be at least 6 characters long</small>
                                                            </div>
                                                            <div className="form-group mb-4">
                                                                <label className="form-label">Confirm New Password <span className="text-danger">*</span></label>
                                                                <input
                                                                    type="password"
                                                                    className="form-control form-control-lg"
                                                                    name="confirm_password"
                                                                    value={passwordData.confirm_password}
                                                                    onChange={handlePasswordInputChange}
                                                                    minLength="6"
                                                                    required
                                                                />
                                                            </div>
                                                            <div className="text-center">
                                                                <button
                                                                    type="submit"
                                                                    className="btn btn-lg px-5"
                                                                    disabled={submitLoading}
                                                                    style={{
                                                                        backgroundImage: "linear-gradient(90deg, #1ec5fa 0%, #0d47d5 50%, #1ec5fa)",
                                                                        border: "none",
                                                                        color: "white"
                                                                    }}
                                                                >
                                                                    <FaLock className="me-2" />
                                                                    {submitLoading ? 'Updating...' : 'Update Password'}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer id="it-nw-footer" className="it-nw-footer-area" style={{ backgroundColor: "#1a1a2e" }}>
                <div className="container">
                    <div className="it-nw-footer-copyright text-center" style={{ padding: "30px 0" }}>
                        <span style={{ color: "#ffffff" }}>
                            © {new Date().getFullYear()} Ente Keralam. Developed by{" "}
                            <a href="https://c-dit.com" target="_blank" rel="noopener noreferrer" style={{ color: "#1ec5fa" }}>
                                Centre for Development of Imaging Technology (C-DIT)
                            </a>
                        </span>
                    </div>
                </div>
            </footer>
        </>
    );
}
