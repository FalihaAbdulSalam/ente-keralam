import { useState, useEffect } from 'react';
import DashboardLayout from './DashboardLayout';
import dashboardAPI from '../services/dashboardAPI';
import { FaTimes } from 'react-icons/fa';

export default function SkillsInterests() {
    const [formData, setFormData] = useState({
        skills: [],
        interests: [],
        expertise_level: 'beginner',
        occupation: '',
        industry: '',
        languages: [],
        hobbies: [],
    });

    const [currentInput, setCurrentInput] = useState({
        skill: '',
        interest: '',
        language: '',
        hobby: ''
    });

    const [loading, setLoading] = useState(false);
    const [initialLoading, setInitialLoading] = useState(true);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState('');

    // Load data on mount
    useEffect(() => {
        loadSkillsInterests();
    }, []);

    const loadSkillsInterests = async () => {
        try {
            setInitialLoading(true);
            const response = await dashboardAPI.getSkillsInterests();
            if (response.data.data) {
                setFormData(response.data.data);
            }
        } catch (err) {
            console.error('Failed to load skills and interests:', err);
            setError('Failed to load data. Using defaults.');
        } finally {
            setInitialLoading(false);
        }
    };

    // Predefined suggestions
    const skillSuggestions = [
        'Web Development', 'Mobile Development', 'Data Analysis', 'Graphic Design',
        'Content Writing', 'Video Editing', 'Photography', 'Public Speaking',
        'Project Management', 'Marketing', 'Social Media', 'Teaching'
    ];

    const interestSuggestions = [
        'Politics', 'Technology', 'Environment', 'Education', 'Healthcare',
        'Sports', 'Arts & Culture', 'Science', 'Economics', 'Social Work',
        'Youth Development', 'Women Empowerment', 'Rural Development'
    ];

    const languageSuggestions = [
        'Malayalam', 'English', 'Hindi', 'Tamil', 'Kannada', 'Telugu'
    ];

    const industrySuggestions = [
        'Information Technology', 'Education', 'Healthcare', 'Government',
        'Agriculture', 'Manufacturing', 'Retail', 'Finance', 'Media',
        'Non-Profit', 'Student', 'Self-Employed', 'Other'
    ];

    const handleAddItem = (type) => {
        const value = currentInput[type].trim();
        if (value && !formData[type + 's'].includes(value)) {
            setFormData(prev => ({
                ...prev,
                [type + 's']: [...prev[type + 's'], value]
            }));
            setCurrentInput(prev => ({ ...prev, [type]: '' }));
        }
    };

    const handleRemoveItem = (type, item) => {
        setFormData(prev => ({
            ...prev,
            [type + 's']: prev[type + 's'].filter(i => i !== item)
        }));
    };

    const handleSuggestionClick = (type, suggestion) => {
        if (!formData[type + 's'].includes(suggestion)) {
            setFormData(prev => ({
                ...prev,
                [type + 's']: [...prev[type + 's'], suggestion]
            }));
        }
    };

    const handleKeyPress = (e, type) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleAddItem(type);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setSuccess(false);

        try {
            const response = await dashboardAPI.updateSkillsInterests(formData);
            
            setSuccess(true);
            setTimeout(() => setSuccess(false), 3000);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to update skills and interests');
        } finally {
            setLoading(false);
        }
    };

    if (initialLoading) {
        return (
            <DashboardLayout>
                <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '400px' }}>
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                </div>
            </DashboardLayout>
        );
    }

    return (
        <DashboardLayout>
            <style>{`
                .skills-wrapper {
                    background: rgba(255, 255, 255, 0.9);
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .skills-container {
                    max-width: 900px;
                    margin: 0 auto;
                }

                .skills-header h3 {
                    margin-bottom: 10px;
                    color: #333;
                    font-size: 24px;
                }

                .skills-header p {
                    color: #666;
                    margin-bottom: 30px;
                }

                .form-section {
                    margin-bottom: 30px;
                }

                .form-section-title {
                    font-size: 18px;
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 15px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #f0f0f0;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 8px;
                    color: #555;
                    font-weight: 500;
                    font-size: 14px;
                }

                .form-control {
                    width: 100%;
                    padding: 12px 15px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                }

                .form-control:focus {
                    outline: none;
                    border-color: #4b7bec;
                }

                .form-select {
                    width: 100%;
                    padding: 12px 15px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                    background: white;
                    cursor: pointer;
                }

                .form-select:focus {
                    outline: none;
                    border-color: #4b7bec;
                }

                .input-with-button {
                    display: flex;
                    gap: 10px;
                }

                .input-with-button input {
                    flex: 1;
                }

                .btn-add {
                    padding: 12px 20px;
                    background: #4b7bec;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 500;
                    white-space: nowrap;
                }

                .btn-add:hover {
                    background: #3867d6;
                }

                .tags-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-top: 10px;
                    min-height: 40px;
                }

                .tag {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 15px;
                    background: #e8f0fe;
                    color: #4b7bec;
                    border-radius: 20px;
                    font-size: 14px;
                    font-weight: 500;
                }

                .tag-remove {
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    color: #4b7bec;
                    transition: color 0.2s;
                }

                .tag-remove:hover {
                    color: #e74c3c;
                }

                .suggestions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    margin-top: 10px;
                }

                .suggestion-chip {
                    padding: 6px 12px;
                    background: #f5f5f5;
                    color: #666;
                    border: 1px solid #ddd;
                    border-radius: 15px;
                    font-size: 13px;
                    cursor: pointer;
                    transition: all 0.2s;
                }

                .suggestion-chip:hover {
                    background: #4b7bec;
                    color: white;
                    border-color: #4b7bec;
                }

                .expertise-options {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 10px;
                }

                .expertise-option {
                    padding: 12px;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.2s;
                    background: white;
                }

                .expertise-option:hover {
                    border-color: #4b7bec;
                }

                .expertise-option.active {
                    border-color: #4b7bec;
                    background: #e8f0fe;
                    font-weight: 600;
                    color: #4b7bec;
                }

                .expertise-option input[type="radio"] {
                    display: none;
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
                    margin-top: 20px;
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

                .helper-text {
                    font-size: 13px;
                    color: #666;
                    margin-top: 5px;
                }

                @media (max-width: 768px) {
                    .skills-wrapper {
                        padding: 25px;
                    }

                    .input-with-button {
                        flex-direction: column;
                    }

                    .btn-add {
                        width: 100%;
                    }
                }
            `}</style>

            <div className="skills-wrapper">
                <div className="skills-container">
                    <div className="skills-header">
                        <h3>Skills & Interests</h3>
                        <p>Tell us about your skills, interests, and expertise</p>
                    </div>

                    {success && (
                        <div className="alert alert-success">
                            ✓ Skills and interests updated successfully!
                        </div>
                    )}

                    {error && (
                        <div className="alert alert-danger">
                            {error}
                        </div>
                    )}

                    <form onSubmit={handleSubmit}>
                        {/* Skills Section */}
                        <div className="form-section">
                            <h4 className="form-section-title">Skills</h4>
                            
                            <div className="form-group">
                                <label>Add Your Skills</label>
                                <div className="input-with-button">
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Type a skill and press Enter or click Add"
                                        value={currentInput.skill}
                                        onChange={(e) => setCurrentInput(prev => ({ ...prev, skill: e.target.value }))}
                                        onKeyPress={(e) => handleKeyPress(e, 'skill')}
                                    />
                                    <button 
                                        type="button" 
                                        className="btn-add"
                                        onClick={() => handleAddItem('skill')}
                                    >
                                        Add
                                    </button>
                                </div>
                                <p className="helper-text">Popular suggestions:</p>
                                <div className="suggestions">
                                    {skillSuggestions.map((suggestion, index) => (
                                        <span
                                            key={index}
                                            className="suggestion-chip"
                                            onClick={() => handleSuggestionClick('skill', suggestion)}
                                        >
                                            {suggestion}
                                        </span>
                                    ))}
                                </div>
                                <div className="tags-container">
                                    {formData.skills.map((skill, index) => (
                                        <span key={index} className="tag">
                                            {skill}
                                            <span 
                                                className="tag-remove"
                                                onClick={() => handleRemoveItem('skill', skill)}
                                            >
                                                <FaTimes size={12} />
                                            </span>
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Interests Section */}
                        <div className="form-section">
                            <h4 className="form-section-title">Interests</h4>
                            
                            <div className="form-group">
                                <label>Add Your Interests</label>
                                <div className="input-with-button">
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Type an interest and press Enter or click Add"
                                        value={currentInput.interest}
                                        onChange={(e) => setCurrentInput(prev => ({ ...prev, interest: e.target.value }))}
                                        onKeyPress={(e) => handleKeyPress(e, 'interest')}
                                    />
                                    <button 
                                        type="button" 
                                        className="btn-add"
                                        onClick={() => handleAddItem('interest')}
                                    >
                                        Add
                                    </button>
                                </div>
                                <p className="helper-text">Popular suggestions:</p>
                                <div className="suggestions">
                                    {interestSuggestions.map((suggestion, index) => (
                                        <span
                                            key={index}
                                            className="suggestion-chip"
                                            onClick={() => handleSuggestionClick('interest', suggestion)}
                                        >
                                            {suggestion}
                                        </span>
                                    ))}
                                </div>
                                <div className="tags-container">
                                    {formData.interests.map((interest, index) => (
                                        <span key={index} className="tag">
                                            {interest}
                                            <span 
                                                className="tag-remove"
                                                onClick={() => handleRemoveItem('interest', interest)}
                                            >
                                                <FaTimes size={12} />
                                            </span>
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Professional Info Section */}
                        <div className="form-section">
                            <h4 className="form-section-title">Professional Information</h4>
                            
                            <div className="form-group">
                                <label>Expertise Level</label>
                                <div className="expertise-options">
                                    <label className={`expertise-option ${formData.expertise_level === 'beginner' ? 'active' : ''}`}>
                                        <input
                                            type="radio"
                                            name="expertise_level"
                                            value="beginner"
                                            checked={formData.expertise_level === 'beginner'}
                                            onChange={(e) => setFormData(prev => ({ ...prev, expertise_level: e.target.value }))}
                                        />
                                        Beginner
                                    </label>
                                    <label className={`expertise-option ${formData.expertise_level === 'intermediate' ? 'active' : ''}`}>
                                        <input
                                            type="radio"
                                            name="expertise_level"
                                            value="intermediate"
                                            checked={formData.expertise_level === 'intermediate'}
                                            onChange={(e) => setFormData(prev => ({ ...prev, expertise_level: e.target.value }))}
                                        />
                                        Intermediate
                                    </label>
                                    <label className={`expertise-option ${formData.expertise_level === 'expert' ? 'active' : ''}`}>
                                        <input
                                            type="radio"
                                            name="expertise_level"
                                            value="expert"
                                            checked={formData.expertise_level === 'expert'}
                                            onChange={(e) => setFormData(prev => ({ ...prev, expertise_level: e.target.value }))}
                                        />
                                        Expert
                                    </label>
                                </div>
                            </div>

                            <div className="form-group">
                                <label>Occupation</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="e.g., Software Engineer, Teacher, Student"
                                    value={formData.occupation}
                                    onChange={(e) => setFormData(prev => ({ ...prev, occupation: e.target.value }))}
                                />
                            </div>

                            <div className="form-group">
                                <label>Industry / Sector</label>
                                <select
                                    className="form-select"
                                    value={formData.industry}
                                    onChange={(e) => setFormData(prev => ({ ...prev, industry: e.target.value }))}
                                >
                                    <option value="">Select Industry</option>
                                    {industrySuggestions.map((industry, index) => (
                                        <option key={index} value={industry}>{industry}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Languages Section */}
                        <div className="form-section">
                            <h4 className="form-section-title">Languages</h4>
                            
                            <div className="form-group">
                                <label>Languages You Know</label>
                                <p className="helper-text">Quick add:</p>
                                <div className="suggestions">
                                    {languageSuggestions.map((suggestion, index) => (
                                        <span
                                            key={index}
                                            className="suggestion-chip"
                                            onClick={() => handleSuggestionClick('language', suggestion)}
                                        >
                                            {suggestion}
                                        </span>
                                    ))}
                                </div>
                                <div className="input-with-button" style={{ marginTop: '10px' }}>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Add other languages"
                                        value={currentInput.language}
                                        onChange={(e) => setCurrentInput(prev => ({ ...prev, language: e.target.value }))}
                                        onKeyPress={(e) => handleKeyPress(e, 'language')}
                                    />
                                    <button 
                                        type="button" 
                                        className="btn-add"
                                        onClick={() => handleAddItem('language')}
                                    >
                                        Add
                                    </button>
                                </div>
                                <div className="tags-container">
                                    {formData.languages.map((language, index) => (
                                        <span key={index} className="tag">
                                            {language}
                                            <span 
                                                className="tag-remove"
                                                onClick={() => handleRemoveItem('language', language)}
                                            >
                                                <FaTimes size={12} />
                                            </span>
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Hobbies Section */}
                        <div className="form-section">
                            <h4 className="form-section-title">Hobbies</h4>
                            
                            <div className="form-group">
                                <label>Your Hobbies</label>
                                <div className="input-with-button">
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Add your hobbies"
                                        value={currentInput.hobby}
                                        onChange={(e) => setCurrentInput(prev => ({ ...prev, hobby: e.target.value }))}
                                        onKeyPress={(e) => handleKeyPress(e, 'hobby')}
                                    />
                                    <button 
                                        type="button" 
                                        className="btn-add"
                                        onClick={() => handleAddItem('hobby')}
                                    >
                                        Add
                                    </button>
                                </div>
                                <div className="tags-container">
                                    {formData.hobbies.map((hobby, index) => (
                                        <span key={index} className="tag">
                                            {hobby}
                                            <span 
                                                className="tag-remove"
                                                onClick={() => handleRemoveItem('hobby', hobby)}
                                            >
                                                <FaTimes size={12} />
                                            </span>
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            className="btn-primary"
                            disabled={loading}
                        >
                            {loading ? 'Saving...' : 'Save Skills & Interests'}
                        </button>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
