import { createContext, useContext, useState, useEffect, lazy, Suspense } from "react";
import { BrowserRouter as Router, Routes, Route, Navigate, useLocation } from "react-router-dom";
import { Helmet, HelmetProvider } from 'react-helmet-async';
import { authAPI } from '../services/api';
import { usePageType } from '../hooks/usePageType';
import { LanguageProvider } from './LanguageContext';

// Core components - loaded eagerly (always needed)
import Header from "./Header";
import Footer from "./Footer";
import PageLayout from "./PageLayout";
import MorphingPreloader from "./MorphingPreloader";
// Carousel is needed for homepage - import eagerly with named exports
import Carousel, { CarouselLoadingProvider, useCarouselLoading } from "./Carousel";
// These are used by PageLayout (which is eager), so import eagerly to avoid conflict
import CircleMenu from "./CircleMenu";
import ScrollTop from "./ScrollTop";

// Lazy loaded components - split into separate chunks
const CreativeThoughts = lazy(() => import("./CreativeThoughts"));
const DailyQuiz = lazy(() => import("./DailyQuiz"));
const ServiceTabs = lazy(() => import("./InvolvedSection"));
const LoginPage = lazy(() => import("./Login"));
const RegisterPage = lazy(() => import("./Register"));
const Dashboard = lazy(() => import("./Dashboard"));
const DashboardContent = lazy(() => import("./DashboardContent"));
const ProfileEdit = lazy(() => import("./ProfileEdit"));
const AccountSettings = lazy(() => import("./AccountSettings"));
const SkillsInterests = lazy(() => import("./SkillsInterests"));
const WinnersSection = lazy(() => import("./WinnersSection"));
const DiscussionBreadcrumb = lazy(() => import("./Poll/PollDetails"));
const PrivacyPolicy = lazy(() => import("./PrivacyPolicy"));
const DataDeletion = lazy(() => import("./DataDeletion"));
const ListingSection = lazy(() => import("./List"));
const QuizListing = lazy(() => import("./Quiz/QuizList"));
const QuizDetails = lazy(() => import("./Quiz/QuizDetails"));
const QuizPage = lazy(() => import("./Quiz/QuizPage"));
const ResultPage = lazy(() => import("./Quiz/ResultPage"));
const QuizResultList = lazy(() => import("./Quiz/QuizResultList"));
const PledgePage = lazy(() => import("./Pledge/Pledge"));
const PledgeListingSection = lazy(() => import("./Pledge/Pledge-list"));
const PledgeResultPage = lazy(() => import("./Pledge/PledgeResult"));
const NewsFeed = lazy(() => import("./Details"));
const TaskList = lazy(() => import("./TaskList"));
const TaskDetails = lazy(() => import("./TaskDetails"));
const FaqPage = lazy(() => import("./Faq"));
const UpdateEmail = lazy(() => import("./UpdateEmail"));
const UpdateMobile = lazy(() => import("./UpdateMobile"));
const UpdatePicture = lazy(() => import("./UpdatePicture"));
const UpdatePassword = lazy(() => import("./UpdatePassword"));
const ForgotPassword = lazy(() => import("./ForgotPassword"));
const ReferralCode = lazy(() => import("./ReferralCode"));
const DeactivateAccount = lazy(() => import("./DeactivateAccount"));
const CreativeGallery = lazy(() => import("./CreativeGallery"));
const DepartmentDetails = lazy(() => import("./DepartmentDetails"));
const InsightsPage = lazy(() => import("./Insight"));
const EconomySection = lazy(() => import("./Economy"));
const Discussion = lazy(() => import("./Discussion"));
const DeptDetails = lazy(() => import("./DeptDetails"));
const NewsFeedSection = lazy(() => import("./Social"));
const CompetitionResultList = lazy(() => import("./Competition/CompetitionResultList"));
const CompetitionList = lazy(() => import("./Competition/CompetitionList"));
const Cards = lazy(() => import("./Cards"));
const SearchPage = lazy(() => import("./SearchPage"));
const PollListingSection = lazy(() => import("./Poll/PollList"));
const CompetitionDetails = lazy(() => import("./Competition/CompetitionDetails"));
const NotFound = lazy(() => import("./NotFound"));

// Auth Context
const AuthContext = createContext();

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(localStorage.getItem('token'));
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (token) {
      // Try to get user from localStorage first for instant display
      const storedUser = localStorage.getItem('user');
      if (storedUser) {
        try {
          setUser(JSON.parse(storedUser));
        } catch (e) {
          // If parsing fails, ignore
        }
      }
      
      // Always fetch fresh data from API to sync any server-side changes
      authAPI.user()
        .then(response => {
          setUser(response.data.user);
          // Update localStorage with fresh data
          localStorage.setItem('user', JSON.stringify(response.data.user));
        })
        .catch(() => {
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          setToken(null);
        })
        .finally(() => {
          setLoading(false);
        });
    } else {
      setLoading(false);
    }
  }, [token]);

  const login = async (credentials) => {
    try {
      const response = await authAPI.login(credentials);
      
      const { user, token } = response.data;
      
      setUser(user);
      setToken(token);
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));
      
      return { success: true };
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed' 
      };
    }
  };

  const register = async (userData) => {
    try {
      const response = await authAPI.register(userData);
      const { user, token } = response.data;
      
      setUser(user);
      setToken(token);
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));
      
      return { success: true };
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Registration failed',
        errors: error.response?.data?.errors || {}
      };
    }
  };

  const authenticateWithToken = (user, token) => {
    setUser(user);
    setToken(token);
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    return { success: true };
  };

  const logout = async () => {
    try {
      await authAPI.logout();
    } catch (error) {
    } finally {
      setUser(null);
      setToken(null);
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
  };

  const value = {
    user,
    setUser,
    token,
    login,
    register,
    authenticateWithToken,
    logout,
    isAuthenticated: !!token,
    loading
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

// Loading component with intelligent icon selection (used inside Router)
function AppLoader() {
  const pageType = usePageType();
  return <MorphingPreloader type={pageType} />;
}

// Simple loading component (used outside Router during initial app load)
// Detects page type from window.location since Router context isn't available yet
function SimpleLoader() {
  const path = window.location.pathname.toLowerCase();
  
  // Determine type from current URL (same logic as usePageType)
  // Order matters - check most specific patterns first to avoid conflicts
  let type = null;
  
  // Quiz-related pages (must come first to prevent /poll/ from matching first)
  if (path.includes('/quiz')) {
    type = 'quiz';
  } 
  // Poll/Survey-related pages
  else if (path.includes('/poll') || path.includes('/survey')) {
    type = 'poll';
  } 
  // Competition-related pages
  else if (path.includes('/competition')) {
    type = 'competition';
  } 
  // Pledge-related pages
  else if (path.includes('/pledge')) {
    type = 'pledge';
  } 
  // Task-related pages
  else if (path.includes('/task')) {
    type = 'task';
  } 
  // Discussion-related pages
  else if (path.includes('/discussion') || path.includes('/discuss')) {
    type = 'discussion';
  }
  
  return <MorphingPreloader type={type} />;
}

// Scroll to top component
function ScrollToTop() {
  const location = useLocation();

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [location]);

  return null;
}

// Protect routes that require authentication
function RequireAuth({ children }) {
  const { isAuthenticated, loading } = useAuth();
  const location = useLocation();

  if (loading) return <AppLoader />;
  if (!isAuthenticated) {
    return (
      <Navigate
        to="/login"
        replace
        state={{ from: location.pathname + location.search }}
      />
    );
  }
  return children;
}

// Schema.org Structured Data Component
function SchemaComponent() {
  const baseUrl = window.location.origin;
  const logoUrl = `${baseUrl}/ente-keralam.png`;
  const searchUrl = `${baseUrl}/search?q={search_term_string}`;

  const schemas = [
    // 1. GovernmentOrganization Schema
    {
      "@context": "https://schema.org",
      "@type": "GovernmentOrganization",
      "name": "Information and Public Relations Department, Government of Kerala",
      "url": baseUrl,
      "logo": logoUrl,
      "sameAs": ["https://kerala.gov.in/"],
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Thiruvananthapuram",
        "addressRegion": "Kerala",
        "addressCountry": "India"
      }
    },
    // 2. WebSite Schema
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Ente Keralam Portal",
      "url": baseUrl,
      "potentialAction": {
        "@type": "SearchAction",
        "target": searchUrl,
        "query-input": "required name=search_term_string"
      }
    },
    // 3. WebPage Schema (Home Page)
    {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "name": "Ente Keralam Citizen Engagement Portal",
      "url": baseUrl,
      "isPartOf": {
        "@type": "WebSite",
        "name": "Ente Keralam Portal",
        "url": baseUrl
      }
    },
    // 4. GovernmentService Schema
    {
      "@context": "https://schema.org",
      "@type": "GovernmentService",
      "name": "Ente Keralam Citizen Engagement Services",
      "serviceType": "Citizen Participation and Government Engagement",
      "provider": {
        "@type": "GovernmentOrganization",
        "name": "Information and Public Relations Department, Government of Kerala"
      },
      "areaServed": {
        "@type": "AdministrativeArea",
        "name": "Kerala, India"
      },
      "url": baseUrl
    },
    // 5. Breadcrumb Schema
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": baseUrl
      }]
    }
  ];

  return (
    <Helmet>
      {schemas.map((schema, index) => (
        <script key={index} type="application/ld+json">
          {JSON.stringify(schema)}
        </script>
      ))}
    </Helmet>
  );
}

function App() {
  const [isAppLoading, setIsAppLoading] = useState(true);

  useEffect(() => {
    // Minimal initial delay for smooth app mount
    const timer = setTimeout(() => {
      setIsAppLoading(false);
    }, 100);

    return () => clearTimeout(timer);
  }, []);

  if (isAppLoading) {
    return <SimpleLoader />;
  }

  return (
    <HelmetProvider>
      <AuthProvider>
        <LanguageProvider>
          <Router>
            <SchemaComponent />
          <ScrollToTop />
          <Suspense fallback={<MorphingPreloader />}>
          <Routes>
            {/* Login & Register without header/footer */}
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />
            <Route path="/forgot-password" element={<ForgotPassword />} />
            <Route path="/privacy-policy" element={<PrivacyPolicy />} />
            <Route path="/data-deletion" element={<DataDeletion />} />
            
            {/* Dashboard and Profile pages with header/footer */}
            <Route 
              path="/dashboard" 
              element={
                <RequireAuth>
                  <PageLayout>
                    <DashboardContent />
                  </PageLayout>
                </RequireAuth>
            } 
          />
          <Route 
            path="/profile/edit" 
            element={
              <RequireAuth>
                <PageLayout>
                  <ProfileEdit />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/update-email" 
            element={
              <RequireAuth>
                <PageLayout>
                  <UpdateEmail />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/update-mobile" 
            element={
              <RequireAuth>
                <PageLayout>
                  <UpdateMobile />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/update-picture" 
            element={
              <RequireAuth>
                <PageLayout>
                  <UpdatePicture />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/update-password" 
            element={
              <RequireAuth>
                <PageLayout>
                  <UpdatePassword />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/referral" 
            element={
              <RequireAuth>
                <PageLayout>
                  <ReferralCode />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/deactivate" 
            element={
              <RequireAuth>
                <PageLayout>
                  <DeactivateAccount />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/account-settings" 
            element={
              <RequireAuth>
                <PageLayout>
                  <AccountSettings />
                </PageLayout>
              </RequireAuth>
            } 
          />
          <Route 
            path="/skills-interests" 
            element={
              <RequireAuth>
                <PageLayout>
                  <SkillsInterests />
                </PageLayout>
              </RequireAuth>
            } 
          />

          {/* Inner pages with header/footer */}
          <Route
            path="/poll-details/:id?"
            element={
              <PageLayout>
                <DiscussionBreadcrumb />
              </PageLayout>
            }
          /> 
          <Route
            path="/polls"
            element={
              <PageLayout>
                {/* <ListingSection type="polls" /> */}
                <PollListingSection />
              </PageLayout>
            }
          /> 
          <Route
            path="/quiz-list"
            element={
              <PageLayout>
                <QuizListing />
              </PageLayout>
            }
          />
          <Route
            path="/quiz-details/:id"
            element={
              <PageLayout>
                <QuizDetails />
              </PageLayout>
            }
          />
          <Route
            path="/quiz/:id"
            element={
              <PageLayout>
                <QuizPage />
              </PageLayout>
            }
          />
          <Route
            path="/quiz-result"
            element={
              <PageLayout>
                <ResultPage />
              </PageLayout>
            }
          />
          <Route
            path="/quiz-results"
            element={
              <PageLayout>
                <QuizResultList />
              </PageLayout>
            }
          />
          <Route
            path="/quiz-result-list"
            element={
              <PageLayout>
                <QuizResultList />
              </PageLayout>
            }
          />
          <Route
            path="/pledge/:id"
            element={
              // <PageLayout>
                <PledgePage />
              // </PageLayout>
            }
          />
          <Route
            path="/pledge-list"
            element={
              <PageLayout>
                <PledgeListingSection />
              </PageLayout>
            }
          />
          <Route
            path="/pledge-result"
            element={
              <PageLayout>
                <PledgeResultPage />
              </PageLayout>
            }
          />
          <Route
            path="/news/:id?"
            element={
              <PageLayout>
                <NewsFeed />
              </PageLayout>
            }
          />
          <Route
            path="/tasks"
            element={
              <PageLayout>
                <TaskList />
              </PageLayout>
            }
          /> 
          <Route
            path="/task-details/:id?"
            element={
              <PageLayout>
                <TaskDetails />
              </PageLayout>
            }
          />
          <Route
            path="/faq"
            element={
              <PageLayout>
                <FaqPage />
              </PageLayout>
            }
          />
          <Route
            path="/creative-gallery"
            element={
              <PageLayout>
                <CreativeGallery />
              </PageLayout>
            }
          />
          <Route
            path="/department-detail"
            element={
              <PageLayout>
                <DepartmentDetails />
              </PageLayout>
            }
          />
          <Route
            path="/insights"
            element={
              <PageLayout>
                <InsightsPage />
              </PageLayout>
            }
          />
          <Route
            // path="/department-inner"
            path="/:sectorSlug"
            element={
              <PageLayout>
                <EconomySection />
              </PageLayout>
            }
          />
          <Route
            path="/discussion/:id?"
            element={
              <PageLayout>
                <Discussion />
              </PageLayout>
            }
          />
          <Route
            path="/dept-detail"
            element={
              <PageLayout>
                <DeptDetails />
              </PageLayout>
            }
          />
          <Route
            path="/search"
            element={
              <PageLayout>
                <SearchPage />
              </PageLayout>
            }
          />
          <Route
            path="/socials"
            element={
              <PageLayout>
                <NewsFeedSection />
              </PageLayout>
            }
          />
          <Route
            path="/competition-result-list"
            element={
              <PageLayout>
                <CompetitionResultList />
              </PageLayout>
            }
          />
          <Route
            path="/competition-list"
            element={
              <PageLayout>
                <CompetitionList />
              </PageLayout>
            }
          /> 
          <Route
            path="/competition-details/:id"
            element={
              <PageLayout>
                <CompetitionDetails />
              </PageLayout>
            }
          />
          <Route
            path="/task-list"
            element={
              <PageLayout>
                <TaskList />
              </PageLayout>
            }
          />
          <Route
            path="/task-detail"
            element={
              <PageLayout>
                <TaskDetails />
              </PageLayout>
            }
          />
          <Route
            path="/details"
            element={
              <PageLayout>
                <NewsFeed />
              </PageLayout>
            }
          />
          <Route
            path="/list"
            element={
              <PageLayout>
                <ListingSection />
              </PageLayout>
            }
          />
          <Route
            path="/result"
            element={
              <PageLayout>
                <ResultPage />
              </PageLayout>
            }
          />
          <Route path="/cards" element={<Cards />} />

          <Route
            path="/"
            element={
              <CarouselLoadingProvider>
                <HomePage />
              </CarouselLoadingProvider>
            }
          />

          {/* 404 - Catch all undefined routes */}
          <Route path="*" element={<NotFound />} />
          </Routes>
        </Suspense>
          </Router>
        </LanguageProvider>
      </AuthProvider>
    </HelmetProvider>
  );
}

// HomePage component with blur effect
function HomePage() {
  const { imagesLoaded } = useCarouselLoading();

  return (
    <>
      {/* Show morphing preloader until carousel images are loaded */}
      {!imagesLoaded && <SimpleLoader />}
      
      <div className={`homepage-wrapper ${!imagesLoaded ? 'loading' : ''}`}>
        <Header />
        <Carousel />
        <ServiceTabs />
        <CreativeThoughts />
        <DailyQuiz />
        <WinnersSection />
        <CircleMenu items={[
          { title: "Competition", image: "/design/assets/new/c-comp-red.svg" },
          { title: "Group Discussion", image: "/design/assets/new/c-diss-blue.svg" },
          { title: "Pledge", image: "/design/assets/new/c-pledg-pista.svg" },
          { title: "Poll/Survey", image: "/design/assets/new/c-poll-grn.svg" },
          { title: "Quiz", image: "/design/assets/new/c-qiz-pink.svg" },
          { title: "To-Do Task", image: "/design/assets/new/c-task-sky.svg" },
          { title: "Pledge", image: "/design/assets/new/c-pledg-pista.svg" },
          { title: "Group Discussion", image: "/design/assets/new/c-diss-blue.svg" },
        ]} />
        <ScrollTop />
        <Footer />
      </div>
    </>
  );
}

export default App;
