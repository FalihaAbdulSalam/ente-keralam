import axios from 'axios';

// Create axios instance with base configuration
const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// Add request interceptor to include auth token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add response interceptor to handle errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            // Only redirect to login if we're not already there
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

// Auth API
export const authAPI = {
    register: (data) => api.post('/register', data),
    login: (data) => api.post('/login', data),
    logout: () => api.post('/logout'),
    user: () => api.get('/user'),
    socialRedirect: (provider) => api.get(`/auth/${provider}`),
    sendOTP: (data) => api.post('/auth/send-otp', data),
    verifyOTP: (data) => api.post('/auth/verify-otp', data),
    registrationSendOTP: (data) => api.post('/register/send-otp', data),
    registrationVerifyOTP: (data) => api.post('/register/verify-otp', data),
    forgotPasswordSendOTP: (data) => api.post('/forgot-password/send-otp', data),
    forgotPasswordVerifyOTP: (data) => api.post('/forgot-password/verify-otp', data),
    resetPasswordWithOTP: (data) => api.post('/forgot-password/reset', data),
    sendUpdateMobileOTP: (data) => api.post('/user/mobile/send-otp', data),
    updateMobileWithOTP: (data) => api.post('/user/mobile/update', data),
    sendUpdateEmailOTP: (data) => api.post('/user/email/send-otp', data),
    updateEmailWithOTP: (data) => api.post('/user/email/update', data),
};

// Polls API
export const pollsAPI = {
    getAll: (params = {}) => api.get('/allpolldata', { params }),
    getById: (id) => api.get(`/polls/${id}`),
    getPollData: (id) => api.get(`/poll/${id}`), // For detailed poll data
    submitVote: (id, data) => api.post(`/poll/${id}/vote`, data),
    submitResponse: (data) => api.post('/pollresponse', data), // Submit poll response with auto-save support
    create: (data) => api.post('/admin/polls', data),
    update: (id, data) => api.put(`/admin/polls/${id}`, data),
    delete: (id) => api.delete(`/admin/polls/${id}`),
};

// Competition API
export const competitionAPI = {
    // Backend exposes contests at /contests and /contest/{id}
    getAll: (params = {}) => api.get('/contests', { params }),
    getById: (id) => api.get(`/contest/${id}`),
    getBySlug: (slug) => api.get(`/contest-slug/${slug}`),
    submit: (id, data) => api.post(`/contest/${id}/submit`, data),
    uploadPhoto: (data) => api.post('/photo-upload', data),
    uploadReel: (data) => api.post('/reel', data),
    getReel: (params) => api.get('/reel', { params }),
    updateReel: (data) => api.put('/reel', data),
    uploadReelMultipart: (formData) => api.post('/reel-upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    }),
    updateReelMultipart: (formData) => api.post('/reel-update', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    }),
    uploadContestPdf: (formData) =>
        api.post('/contest/upload/pdf/add', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),
    getContestPdfSubmission: (params) =>
        api.get('/contest/upload/pdf', { params }),
    updateContestPdf: (formData) =>
        api.post('/contest/upload/pdf/update', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),
    create: (data) => api.post('/admin/competitions', data),
    update: (id, data) => api.put(`/admin/competitions/${id}`, data),
    delete: (id) => api.delete(`/admin/competitions/${id}`),
};

// Quiz API
export const quizAPI = {
    getAll: (params = {}) => api.get('/allquizdata', { params }),
    getById: (id) => api.get(`/quizzes/${id}`),
    getQuizData: (id) => api.get(`/quizdata/${id}`), // For detailed quiz with questions
    getDailyQuiz: () => api.get('/daily-quiz'),
    getResults: () => api.get('/quiz-results'), // Get completed quiz results
    getResultById: (id) => api.get(`/quiz-results/${id}`), // Get specific quiz result with participants
    submitAnswer: (id, data) => api.post(`/quizzes/${id}/submit`, data),
    submitQuizResponse: (data) => api.post('/quizresponse', data), // Submit quiz responses with user, quiz, and answers
    create: (data) => api.post('/admin/quizzes', data),
    update: (id, data) => api.put(`/admin/quizzes/${id}`, data),
    delete: (id) => api.delete(`/admin/quizzes/${id}`),
};

// Pledge API
export const pledgeAPI = {
    getAll: (params = {}) => api.get('/all_pledge', { params }),
    getById: (id) => api.get(`/pledge/${id}`),
    submit: (id, data) => api.post(`/pledge/${id}/submit`, data),
    create: (data) => api.post('/admin/pledges', data),
    update: (id, data) => api.put(`/admin/pledges/${id}`, data),
    delete: (id) => api.delete(`/admin/pledges/${id}`),
};

// Tasks API
export const tasksAPI = {
    getAll: (params = {}) => api.get('/tasks', { params }),
    getById: (id) => api.get(`/tasks/${id}`),
    create: (data) => api.post('/admin/tasks', data),
    update: (id, data) => api.put(`/admin/tasks/${id}`, data),
    delete: (id) => api.delete(`/admin/tasks/${id}`),
};

// News API
export const newsAPI = {
    getAll: (params = {}) => api.get('/news', { params }),
    getById: (id) => api.get(`/news/${id}`),
    create: (data) => api.post('/admin/news', data),
    update: (id, data) => api.put(`/admin/news/${id}`, data),
    delete: (id) => api.delete(`/admin/news/${id}`),
};

// Testimonials API
export const testimonialsAPI = {
    getAll: (params = {}) => api.get('/testimonials', { params }),
    create: (data) => api.post('/admin/testimonials', data),
    update: (id, data) => api.put(`/admin/testimonials/${id}`, data),
    delete: (id) => api.delete(`/admin/testimonials/${id}`),
};

// Activity Tracking API
export const activityAPI = {
    submitQuiz: (id, data) => api.post(`/activities/quiz/${id}`, data),
    submitPledge: (id, data) => api.post(`/activities/pledge/${id}`, data),
    submitPoll: (id, data) => api.post(`/activities/poll/${id}`, data),
    submitTask: (id, data) => api.post(`/activities/task/${id}`, data),
    submitCompetition: (id, data) => api.post(`/activities/competition/${id}`, data),
    checkCompletion: (type, id) => api.get(`/activities/check/${type}/${id}`),
    getSummary: () => api.get('/activities/summary'),
};

export default api;

// Menus API
export const menusAPI = {
    getAll: () => api.get('/menus'),
};

// Article Types API
export const articleTypesAPI = {
    getAll: () => api.get('/article-types'),
    getById: (id) => api.get(`/article-types/${id}`),
};

// Sectors API (Sector-Wise Insights)
export const sectorsAPI = {
    getAll: () => api.get('/sectors'),
    getById: (id) => api.get(`/sectors/${id}`),
    getArticles: (sectorId) => api.get(`/sectors/${sectorId}/articles`),
};

// Articles API
export const articlesAPI = {
    getAll: (params = {}) => api.get('/articles', { params }),
    getById: (id) => api.get(`/articles/${id}`),
    getBySector: (sectorId, params = {}) => api.get(`/sectors/${sectorId}/articles`, { params }),
};

// Footer API
export const footersAPI = {
    getAll: () => api.get('/footers'),
};

// Banners API
export const bannersAPI = {
    getAll: () => api.get('/banners'),
};

// Creative Thoughts API
export const creativethoughtsAPI = {
    getAll: () => api.get('/creativethought'),
};

// Discussions API (external production API)
const DISCUSSIONS_BASE_URL = 'https://entekeralam.kerala.gov.in/api';
export const discussionsAPI = {
    getAll: (params = {}) => axios.get(`${DISCUSSIONS_BASE_URL}/discussions`, { params }),
    getById: (id) => axios.get(`${DISCUSSIONS_BASE_URL}/discussions/${id}`),
    submitResponse: (data) => axios.post(`${DISCUSSIONS_BASE_URL}/discussions-response`, data, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    }),
};
