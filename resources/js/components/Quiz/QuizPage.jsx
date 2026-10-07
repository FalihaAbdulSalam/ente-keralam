import { useState, useEffect, useRef } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { quizAPI } from "../../services/api";
import { useAuth } from "../App";
import MorphingPreloader from "../MorphingPreloader";
import "./QuizDetails.css";

const QuizPage = () => {
  const navigate = useNavigate();
  const { id } = useParams(); // Get dynamic ID from URL
  const { isAuthenticated, loading: authLoading, user } = useAuth();

  const [quiz, setQuiz] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [currentQ, setCurrentQ] = useState(0);
  // store selected option_id for each question
  const [selectedAnswerIds, setSelectedAnswerIds] = useState([]);
  const [skipped, setSkipped] = useState([]);

  // per-question timer (seconds)
  const [timeLeft, setTimeLeft] = useState(0);
  const timerRef = useRef(null);
  const submitTimeoutRef = useRef(null);

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!authLoading && !isAuthenticated) {
      navigate('/login', { state: { from: `/quiz-details/${id || 1}` } });
    }
  }, [authLoading, isAuthenticated, navigate, id]);

  useEffect(() => {
    let mounted = true;
    setLoading(true);
    
    quizAPI.getQuizData(id || 1)
      .then((response) => {
        if (!mounted) return;
        const q = response.data.quiz;
        setQuiz(q);
        setSelectedAnswerIds(Array(q.total_questions).fill(null));
        setTimeLeft(q.timer || (q.questions && q.questions[0]?.duration) || 10);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        if (!mounted) return;
        setError("Failed to load quiz data");
        setLoading(false);
      });

    return () => {
      mounted = false;
      if (submitTimeoutRef.current) {
        clearTimeout(submitTimeoutRef.current);
      }
    };
  }, [id]); // Re-fetch when ID changes

  // reset timer when current question changes
  useEffect(() => {
    if (!quiz || !quiz.questions) return;

    // determine duration for current question (fallback to quiz.timer)
    const duration = quiz.questions[currentQ]?.duration || quiz.timer || 10;
    setTimeLeft(duration);

    if (timerRef.current) {
      clearInterval(timerRef.current);
    }

    timerRef.current = setInterval(() => {
      setTimeLeft((t) => t - 1);
    }, 1000);

    return () => clearInterval(timerRef.current);
  }, [currentQ, quiz]);

  // when timer runs out, auto-move to next question or submit
  useEffect(() => {
    if (timeLeft === undefined || timeLeft === null) return;
    if (timeLeft <= 0 && quiz) {
      // if no answer selected, mark skipped
      if (!selectedAnswerIds[currentQ]) {
        setSkipped((s) => (s.includes(currentQ) ? s : [...s, currentQ]));
      }

      if (currentQ < quiz.total_questions - 1) {
        setCurrentQ((c) => c + 1);
      } else {
        // inline submit (avoid missing dependency on handleSubmit)
        navigate("/quiz-result", { state: { selectedAnswerIds, quiz, quizId: id } });
      }
    }
    // include dependencies used inside
  }, [timeLeft, quiz, currentQ, selectedAnswerIds, navigate, id]);

  const formatTime = (seconds) => {
    if (seconds == null) return "00:00";
    const m = Math.floor(seconds / 60).toString().padStart(2, "0");
    const s = (seconds % 60).toString().padStart(2, "0");
    return `${m}:${s}`;
  };

  // Format date to YYYY-MM-DD HH:mm:ss format
  const formatDateTime = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  };

  // Submit quiz responses immediately when answer is selected
  const submitQuizResponse = async (updatedAnswers) => {
    try {
      if (!user || !id || !quiz || !quiz.questions?.length) return;

      // Clear any pending submission
      if (submitTimeoutRef.current) {
        clearTimeout(submitTimeoutRef.current);
      }

      // Debounce the API call slightly to avoid too many rapid requests
      submitTimeoutRef.current = setTimeout(async () => {
        const attendingDate = formatDateTime(new Date());
        
        // Format answers array with question_id, selected_option_id, and attending_date
        const answers = quiz.questions.map((question, index) => ({
          question_id: question.question_id || question.id,
          selected_option_id: updatedAnswers?.[index] || null,
          attending_date: attendingDate,
        }));

        const payload = {
          user: {
            user_id: user.id || user.user_id,
            username: user.username || user.name,
            email: user.email,
          },
          quiz: {
            quiz_id: id,
            total_questions: quiz.total_questions || quiz.questions.length,
          },
          answers: answers,
        };

        await quizAPI.submitQuizResponse(payload);
        console.log("Quiz response saved successfully");
      }, 300); // 300ms debounce to avoid too many API calls
    } catch (error) {
      console.error("Error submitting quiz response:", error);
    }
  };

  const handleSelect = (optionId) => {
    const updated = [...selectedAnswerIds];
    updated[currentQ] = optionId;
    setSelectedAnswerIds(updated);
    // if previously skipped, remove from skipped
    if (skipped.includes(currentQ)) {
      setSkipped(skipped.filter((i) => i !== currentQ));
    }
    // Submit response immediately when answer is selected
    submitQuizResponse(updated);
  };

  const handleNext = () => {
    if (!quiz) return;
    if (currentQ < quiz.total_questions - 1) setCurrentQ(currentQ + 1);
  };

  const handlePrev = () => {
    if (currentQ > 0) setCurrentQ(currentQ - 1);
  };

  const handleSkip = () => {
    if (!skipped.includes(currentQ)) setSkipped([...skipped, currentQ]);
    if (quiz && currentQ < quiz.total_questions - 1) setCurrentQ(currentQ + 1);
  };

  const handleSubmit = () => {
    // navigate to result with selectedAnswerIds, quiz data, and id
    navigate("/quiz-result", { state: { selectedAnswerIds, quiz, quizId: id } });
  };

  const handleJumpTo = (index) => {
    setCurrentQ(index);
  };

  if (loading) return <MorphingPreloader type="quiz" />;
  if (error) return <div className="quiz-page-wrapper quiz-font">{error}</div>;
  if (!quiz) return null;

  const q = quiz.questions[currentQ];

  return (
    <> 
      <section id="saasio-breadcurmb" className="bc-section">
        <div className="container">
          <div className="bc-title">
            <h2>{quiz.name || "Quiz"}</h2>
          </div>
        </div>
      </section>

      <div className="quiz-page-wrapper quiz-font">
        <div className="container quiz-container">
          <div className="quiz-left">
            <div className="header-text">Total Questions : {quiz.total_questions}</div>

            <div className="question-list">
              {quiz.questions.map((_, i) => {
                let statusClass = "";
                if (selectedAnswerIds[i]) statusClass = "answered";
                else if (skipped.includes(i)) statusClass = "skipped";

                return (
                  <div
                    key={i}
                    className={`question-box ${statusClass} ${currentQ === i ? "active" : ""}`}
                    onClick={() => handleJumpTo(i)}
                  >
                    {i + 1}
                  </div>
                );
              })}

              <div className="legend-section">
                <div className="legend-item answered-color"></div> <span>Answered</span>
                <div className="legend-item skipped-color"></div> <span>Skipped</span>
              </div>
            </div>

            <div className="disclaimer-box">
              <strong>DISCLAIMER:</strong> Please do not <span className="red-text">Close</span> or <span className="red-text">Refresh</span> this page.
            </div>
          </div>

          <div className="quiz-right">
            <div className="quiz-content">
              <div className="question-header">
                <div className="question-number">Question <span className="question-number-1">{currentQ + 1}</span></div>
                <div className="timer">⏲ {formatTime(timeLeft)}</div>
              </div>

              <p className="question-text">{q.question}</p>

              <div className="options">
                {q.options.map((opt) => (
                  <label key={opt.option_id} className={`option-label ${selectedAnswerIds[currentQ] === opt.option_id ? "selected" : ""}`}>
                    <input
                      type="radio"
                      name={`question-${currentQ}`}
                      value={opt.option_id}
                      checked={selectedAnswerIds[currentQ] === opt.option_id}
                      onChange={() => handleSelect(opt.option_id)}
                    />
                    <span>{opt.option_text}</span>
                  </label>
                ))}
              </div>
            </div>

            <div className="quiz-footer">
              <div className="footer-btn-container">
                <button className="footer-btn skip-btn" onClick={handleSkip}>Skip</button>

                <div className="footer-right-btns">
                  {currentQ > 0 && (
                    <button className="footer-btn prev-btn" onClick={handlePrev}>Previous</button>
                  )}

                  {currentQ === quiz.total_questions - 1 ? (
                    <button className="footer-btn next-btn" onClick={handleSubmit}>Submit</button>
                  ) : (
                    <button className="footer-btn next-btn" onClick={handleNext}>Next</button>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default QuizPage;
