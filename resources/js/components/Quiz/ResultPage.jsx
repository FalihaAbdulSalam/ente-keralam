import { useEffect, useState } from "react";
import { useLocation } from "react-router-dom";
import confetti from "canvas-confetti";
import { useAuth } from "../App";
import { generateQuizCertificate } from "../../utils/certificateGenerator";
import { quizAPI } from "../../services/api";
import "./QuizDetails.css";

const ResultPage = () => {
  const location = useLocation();
  const { user } = useAuth();
  const { selectedAnswerIds, quiz, answers, questions } = location.state || {};
  const [isGeneratingCert, setIsGeneratingCert] = useState(false);
  const [activitySubmitted, setActivitySubmitted] = useState(false);
  const [pointsEarned, setPointsEarned] = useState(null);
  const [submitting, setSubmitting] = useState(false);
  
  // support both new and previous shapes
  const usedAnswers = selectedAnswerIds || answers;
  const usedQuestions = (quiz && quiz.questions) || questions;

  // Robustly determine quizId from multiple possible sources
  const quizIdFromState = location.state?.quizId || location.state?.id;
  const quizIdFromQuiz = quiz?.id || quiz?.quiz_id;
  const quizIdFromQuestions = Array.isArray(usedQuestions)
    ? (usedQuestions[0]?.quiz_id || usedQuestions[0]?.quizId || usedQuestions[0]?.quiz?.id)
    : undefined;
  const quizId = quizIdFromQuiz ?? quizIdFromState ?? quizIdFromQuestions;

  // compute score by comparing selected option_id to question.correct_answer.answer_id
  const score = usedQuestions?.reduce((acc, q, i) => {
    const selected = usedAnswers?.[i];
    const correctId = q?.correct_answer?.answer_id;
    if (selected != null && correctId != null && Number(selected) === Number(correctId)) return acc + 1;
    return acc;
  }, 0) ?? 0;

  // Local fallback points (used only when user isn't authenticated)
  const participationPoints = quiz?.participation_points ?? 0;
  const pointsPerCorrect = quiz?.per_correct_points ?? quiz?.points ?? 0;

  const computedBasePoints = (() => {
    if (!usedQuestions?.length) return 0;
    return participationPoints + (score * pointsPerCorrect);
  })();

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

  // Submit quiz responses to /api/quizresponse (FINAL submission)
  const submitQuizResponse = async () => {
    try {
      if (!user || !quizId || !usedQuestions?.length) return;
      
      const attendingDate = formatDateTime(new Date());
      
      // Format answers array with question_id, selected_option_id, and attending_date
      const answers = usedQuestions.map((question, index) => ({
        question_id: question.question_id || question.id,
        selected_option_id: usedAnswers?.[index] || null,
        attending_date: attendingDate,
      }));

      const payload = {
        user: {
          user_id: user.id || user.user_id,
          username: user.username || user.name,
          email: user.email,
        },
        quiz: {
          quiz_id: quizId,
          total_questions: usedQuestions.length,
        },
        answers: answers,
        is_final_submission: true, // This triggers activity creation and points
      };

      const response = await quizAPI.submitQuizResponse(payload);
      console.log("Quiz response submitted successfully");
      
      // Update points from response if available
      if (response?.data?.success && response?.data?.data?.points_earned !== undefined) {
        setPointsEarned(response.data.data.points_earned);
      }
    } catch (error) {
      console.error("Error submitting quiz response:", error);
    }
  };

  useEffect(() => {
    confetti({
      particleCount: 180,
      spread: 90,
      origin: { y: 0.6 },
    });
    // Submit quiz response with final submission flag - this handles both
    // saving answers AND creating activity/awarding points
    if (!activitySubmitted && user && quizId && usedQuestions?.length) {
      setSubmitting(true);
      submitQuizResponse().finally(() => {
        setActivitySubmitted(true);
        setSubmitting(false);
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user, quizId, usedQuestions, activitySubmitted]);

  if (!usedAnswers || !usedQuestions) {
    return (
      <div className="result-page">
        <div>⚠️ No quiz data found. Please start again.</div>
        <button className="btn" onClick={() => (window.location.href = "/") }>
          Go Home
        </button>
      </div>
    );
  }

  

  // Handle certificate download
  const handleDownloadCertificate = async () => {
    setIsGeneratingCert(true);
    try {
      const userName = user?.name || location.state?.userName || "Participant";
      const quizTitleBase = quiz?.name || "Quiz";
      const quizTitle =  quizTitleBase || quiz.topic;
      
      await generateQuizCertificate(userName, quizTitle, score, usedQuestions.length);
      
      console.log("Certificate generated successfully");
    } catch (error) {
      console.error("Error generating certificate:", error);
      alert("Failed to generate certificate. Please try again.");
    } finally {
      setIsGeneratingCert(false);
    }
  };

  return (
    <>
      {/* Breadcrumb Section */}
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="container">
          <div className="breadcurmb-title">
            <h2>Quiz</h2>
          </div>
          <div className="breadcurmb-item-list ul-li">
            <ul className="saasio-page-breadcurmb">
              <li>
                <a href="#">Home</a>
              </li>
              <li>
                <a href="#">Quizzes</a>
              </li>
            </ul>
          </div>
        </div>
      </section>

      {/* Result Page */}
      <div className="result-page quiz-font">
        <div className="result-box">
          {/* <img
            src="../img/certificate.png"
            alt="Trophy"
            className="trophy-img"
          /> */}
          <img src="/design/assets/OBJECTS.png" alt="Congrats" className="congrats-img" />
          {/* <h1>  <img src="../img/party.gif" alt="Celebration" className="party-gif" />
                Congratulations!</h1> */}
          <h2>
            Your Score: {score} / {usedQuestions.length}
          </h2>
          <div className="subtext">
            You’ve successfully completed the quiz. Great job!
          </div>
          <div className="participation-text">
            <p>
              Points Earned: {
                submitting
                  ? '...'
                  : (pointsEarned !== null
                      ? pointsEarned
                      : (user ? '...' : computedBasePoints))
              }
            </p>
          </div>
          {/* <div className="certificate-text">
            🏅{" "}
            <button 
              onClick={handleDownloadCertificate}
              className="download-link"
              style={{ 
                background: 'none', 
                border: 'none', 
                color: 'inherit', 
                textDecoration: 'underline',
                cursor: isGeneratingCert ? 'wait' : 'pointer',
                padding: 0,
                font: 'inherit'
              }}
              disabled={isGeneratingCert}
            >
              {isGeneratingCert ? "Generating..." : "Click here"}
            </button>{" "}
            to download your certificate
          </div> */}
          <button className=" try-again-btn" onClick={() => (window.location.href = "/")}>
            Go Home
          </button>
        </div>
      </div>
    </>
  );
};

export default ResultPage;
