import axios from 'axios';

const API_URL = '/api';

// Helper function to get auth token from localStorage
const getAuthToken = () => {
  return localStorage.getItem('token');
};

// Helper function to create axios config with auth header
const getAuthConfig = () => {
  const token = getAuthToken();
  return {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json',
    }
  };
};

const dashboardAPI = {
  // Get dashboard data
  getDashboard: () => axios.get(`${API_URL}/dashboard`, getAuthConfig()),
  
  // Get user activities
  getActivities: () => axios.get(`${API_URL}/dashboard/activities`, getAuthConfig()),
  
  // Get user points
  getPoints: () => axios.get(`${API_URL}/dashboard/points`, getAuthConfig()),
  
  // Get profile
  getProfile: () => axios.get(`${API_URL}/profile`, getAuthConfig()),
  
  // Update profile
  updateProfile: (data) => axios.put(`${API_URL}/profile`, data, getAuthConfig()),
  
  // Get profile completion
  getProfileCompletion: () => axios.get(`${API_URL}/profile/completion`, getAuthConfig()),

  // Update profile picture
  updateProfilePicture: (formData) => {
    const token = getAuthToken();
    return axios.post(`${API_URL}/profile/avatar`, formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
      }
    });
  },

  // Remove profile picture
  removeProfilePicture: () => axios.delete(`${API_URL}/profile/avatar`, getAuthConfig()),

  // Update password
  updatePassword: (data) => axios.post(`${API_URL}/profile/password`, data, getAuthConfig()),

  // Get referral code and stats
  getReferralCode: () => axios.get(`${API_URL}/referral`, getAuthConfig()),

  // Deactivate account
  deactivateAccount: (data) => axios.post(`${API_URL}/profile/deactivate`, data, getAuthConfig()),

  // Get account settings
  getSettings: () => axios.get(`${API_URL}/profile/settings`, getAuthConfig()),

  // Update account settings
  updateSettings: (data) => axios.put(`${API_URL}/profile/settings`, data, getAuthConfig()),

  // Get skills and interests
  getSkillsInterests: () => axios.get(`${API_URL}/profile/skills-interests`, getAuthConfig()),

  // Update skills and interests
  updateSkillsInterests: (data) => axios.put(`${API_URL}/profile/skills-interests`, data, getAuthConfig()),

  // Get districts list
  getDistricts: () => axios.get(`${API_URL}/districts`),
};

export default dashboardAPI;
