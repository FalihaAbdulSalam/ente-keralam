import { useEffect, useState } from "react";
import "./PledgePage.css";
import styles from "./pledge.module.css"; // Import the CSS Module
import { useNavigate, useParams } from "react-router-dom";
import { pledgeAPI, activityAPI } from "../../services/api";
import { useAuth } from "../App";
import MorphingPreloader from "../MorphingPreloader";

const PledgePage = () => {
  const navigate = useNavigate();
  const { id } = useParams(); // Get dynamic ID from URL
  const { isAuthenticated, loading: authLoading, user } = useAuth();

  const [pledgeData, setPledgeData] = useState(null);
  const [pledgeText, setPledgeText] = useState("");
  const [highlightIndex, setHighlightIndex] = useState(0);
  const [showConfirm, setShowConfirm] = useState(false);
  const [showDownload, setShowDownload] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [hasCompleted, setHasCompleted] = useState(false);
  const [checkingCompletion, setCheckingCompletion] = useState(true);
  const [completionMessageVisible, setCompletionMessageVisible] = useState(false);

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!authLoading && !isAuthenticated) {
      navigate('/login', { state: { from: `/pledge/${id || 1}` } });
    }
  }, [authLoading, isAuthenticated, navigate, id]);

  // Helper: strip HTML tags to plain text for the animated highlight effect
  const stripHtml = (html) => {
    try {
      const div = document.createElement("div");
      div.innerHTML = html || "";
      return div.textContent || div.innerText || "";
    } catch (e) {
      return html || "";
    }
  };

  // Fetch pledge from API
  useEffect(() => {
    let mounted = true;
    setLoading(true);
    
    pledgeAPI.getById(id || 1)
      .then((response) => {
        if (!mounted) return;
        const data = response.data;
        if (data && data.result && data.data) {
          setPledgeData(data.data);
          setPledgeText(stripHtml(data.data.content || data.data.description || ""));
        } else {
          setError("No pledge data returned from server.");
        }
      })
      .catch((err) => {
        console.error("Failed to fetch pledge", err);
        if (!mounted) return;
        setError("Failed to load pledge.");
      })
      .finally(() => {
        if (mounted) setLoading(false);
      });
    
    return () => {
      mounted = false;
    };
  }, [id]); // Re-fetch when ID changes

  // Check if user has already completed this pledge
  useEffect(() => {
    if (user && id) {
      checkCompletion();
    } else {
      setCheckingCompletion(false);
    }
  }, [user, id]);

  const checkCompletion = async () => {
    try {
      const response = await activityAPI.checkCompletion('pledge', id);
      if (response.data.success) {
        const completed = response.data.data.has_completed;
        setHasCompleted(completed);
        
        // If already completed, show message and redirect
        if (completed) {
          // Show friendly in-page message instead of intrusive alert + redirect
          setCompletionMessageVisible(true);
        }
      }
    } catch (error) {
      console.error("Error checking completion:", error);
    } finally {
      setCheckingCompletion(false);
    }
  };

  // Animated highlight effect; restart when text changes
  useEffect(() => {
    setHighlightIndex(0);
    const words = (pledgeText || "").split(/\s+/).filter(Boolean);
    if (words.length === 0) {
      setShowConfirm(false);
      return;
    }
    let i = 0;
    const interval = setInterval(() => {
      if (i < words.length) {
        setHighlightIndex(i);
        i++;
      } else {
        clearInterval(interval);
        setShowConfirm(true);
      }
    }, 400);
    return () => clearInterval(interval);
  }, [pledgeText]);

  const words = (pledgeText || "").split(/\s+/).filter(Boolean);

  const handleCheckboxChange = (e) => {
    setShowDownload(e.target.checked);
  };

  const handleDownloadCertificate = async () => {
    try {
      // Submit pledge completion to track activity and award points
      const response = await activityAPI.submitPledge(id, {
        pledge_title: pledgeData?.title || "Pledge",
      });

      if (response.data.success) {
        console.log("Pledge activity recorded:", response.data);
        
        // Navigate to result page and pass pledge data with ID and points
        navigate("/pledge-result", { 
          state: { 
            pledge: {
              ...pledgeData,
              score: response.data.data.points_earned
            },
            pledgeId: id 
          } 
        });
      }
    } catch (error) {
      console.error("Error submitting pledge:", error);
      
      // Still navigate to result page even if activity tracking fails
      navigate("/pledge-result", { 
        state: { 
          pledge: pledgeData, 
          pledgeId: id 
        } 
      });
    }
  };

  return (
    <div className={styles.pledgePageContainer}>
      <div className="limiter pledge-container">
        <div className="text-center mb-2 py-3 logio">
          <img src="/design/assets/qsd.svg" alt="Logo" />
        </div>
        <div className="container-login100">
          <div className="col-lg-9 col-12 p-0 mx-auto">
            <div className="col-md-8 col-12 mx-auto text-center">
              <img src={pledgeData?.banner} className="mb-3 w-100" alt="Banner" />
            </div>

            <div className="wrap-login100">
              <div className="row align-items-center">
                <div className="col-md-3">
                  <div className="login100-pic js-tilt"></div>
                </div>
                <div className="col-lg-1"></div>
                <div className="col-lg-8 col-12 bgb-white pledgeTextContainer">
                  {checkingCompletion ? (
                    <p className="pld mal">Checking completion status...</p>
                  ) : completionMessageVisible ? (
                    <div className="pld mal" style={{ textAlign: 'center' }}>
                      <h3 style={{ color: '#2e7d32', marginBottom: '12px' }}>✅ Pledge Already Completed</h3>
                      <p style={{ fontSize: '15px', lineHeight: '22px' }}>
                        You have already completed this pledge earlier. Thank you for your participation!
                      </p>
                      <div style={{ marginTop: '18px', display: 'flex', gap: '10px', justifyContent: 'center', flexWrap: 'wrap' }}>
                        <button
                          className="btn btn-primary btn-sm"
                          onClick={() => navigate('/dashboard')}
                        >
                          Go to Dashboard
                        </button>
                        <button
                          className="btn btn-secondary btn-sm"
                          onClick={() => navigate('/')}
                        >
                          Home
                        </button>
                      </div>
                    </div>
                  ) : loading ? (
                    <MorphingPreloader type="pledge" />
                  ) : error ? (
                    <p className="pld mal">{error}</p>
                  ) : (
                    <>
                      <p id="pledge" className="pld mal">
                        <span id="pledgeText">
                          {words.map((word, i) => (
                            <span key={i} className={i === highlightIndex ? "highlight-pledge" : ""}>
                              {word + " "}
                            </span>
                          ))}
                        </span>
                      </p>
                      {showConfirm && (
                        <div id="confirmDiv" className="mal form-check mt-3">
                          <label className="form-check-label" htmlFor="confirmCheck">
                            <input type="checkbox" id="confirmCheck" className="form-check-input" onChange={handleCheckboxChange} />
                            &nbsp;ഞാൻ പ്രതിജ്ഞ വായിച്ചു മനസ്സിലാക്കി.
                          </label>
                          {showDownload && (
                            <div className="downloadLink">
                              <button className="btn btn-primary pledge-sub" onClick={handleDownloadCertificate}>
                                Submit
                              </button>
                            </div>
                          )}
                        </div>
                      )}
                    </>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PledgePage;
