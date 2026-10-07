import { useEffect, useRef, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { quizAPI, activityAPI } from "../../services/api";
import { useAuth } from "../App";
import MorphingPreloader from "../MorphingPreloader";
import "./QuizDetails.css";

const QuizDetails = () => {
    const navigate = useNavigate();
    const { id } = useParams(); // Get dynamic ID from URL
    const { isAuthenticated, user } = useAuth();
    const [quiz, setQuiz] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [hasCompleted, setHasCompleted] = useState(false);
    const [checkingCompletion, setCheckingCompletion] = useState(true);
    const shapes = [
        "Vector (1).svg",
        "Vector (2).svg",
        "Vector (5).svg",
        "Vector (3).svg",
        "Vector (4).svg",
        "win2.svg",
        "win3.svg",
    ];
    const shapeSizes = {
        "Vector (1).svg": 0,
        "Vector (2).svg": 30,
        "Vector (3).svg": 30,
        "Vector (4).svg": 0,
        "Vector (5).svg": 0,
        "win2.svg": 14,
        "win3.svg": 14,
    };
    const rewardsRef = useRef(null);

    useEffect(() => {
        let mounted = true;
        setLoading(true);

        quizAPI
            .getQuizData(id || 1)
            .then((response) => {
                if (!mounted) return;
                setQuiz(response.data.quiz);
                setLoading(false);
            })
            .catch((err) => {
                console.error(err);
                if (!mounted) return;
                setError("Failed to load quiz details");
                setLoading(false);
            });

        return () => {
            mounted = false;
        };
    }, [id]); // Re-fetch when ID changes

    // Check if user has already completed this quiz
    useEffect(() => {
        if (user && id) {
            checkCompletion();
        } else {
            setCheckingCompletion(false);
        }
    }, [user, id]);

    const checkCompletion = async () => {
        try {
            const response = await activityAPI.checkCompletion("quiz", id);
            if (response.data.success) {
                setHasCompleted(response.data.data.has_completed);
            }
        } catch (error) {
            console.error("Error checking completion:", error);
        } finally {
            setCheckingCompletion(false);
        }
    };

    const handleProceed = () => {
        if (hasCompleted) {
            alert("You have already completed this quiz!");
            return;
        }

        if (isAuthenticated) {
            navigate(`/quiz/${id || 1}`);
        } else {
            // Store the current quiz details page to redirect after login
            navigate("/login", { state: { from: `/quiz-details/${id || 1}` } });
        }
    };

    return (
        <>
            <section
                id="saasio-breadcurmb"
                className="saasio-breadcurmb-section"
            >
                <div className="container">
                    <div className="breadcurmb-title">
                        <h2>Quiz</h2>
                    </div>
                    <div className="breadcurmb-item-list ul-li">
                        <ul className="saasio-page-breadcurmb">
                            <li>
                                <a href="/">Home</a>
                            </li>
                            <li>
                                <a href="/quiz-list">Quiz</a>
                            </li>
                            <li>
                                <a href="#">Quiz Details</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* Quiz Section */}
            <section
                id="news-feed"
                className="news-feed-section quiz-font position-relative"
            >
                <div className="blog-shapes-grid-right">
                    {Array.from({ length: 200 }).map((_, i) => {
                        const file = shapes[i % shapes.length];
                        return (
                            <div
                                className={`it-nw-blog-sh-bg sh${(i % 3) + 1}`}
                                key={i}
                            >
                                <img
                                    src={`/design/assets/bgv/${file}`}
                                    alt=""
                                    style={{
                                        width: shapeSizes[file] + "px",
                                    }}
                                />
                            </div>
                        );
                    })}
                </div>
                <div className="container">
                    {loading && <MorphingPreloader type="quiz" />}
                    {error && <div className="text-danger">{error}</div>}
                    {!loading && quiz && (
                        <div className="blog-feed-content pollDet">
                            <div className="row">
                                <div className="col-md-12">
                                    <div className="saasio-blog-details-content">
                                        {/* Title and Meta Info */}
                                        <div className="blog-details-text dia-headline">
                                            <div
                                                className="d-flex align-items-center justify-content-between flex-wrap gap-3"
                                                style={{ marginBottom: "10px" }}
                                            >
                                                <div className="d-flex align-items-center gap-2">
                                                    <img
                                                        src="/design/assets/online-exam.gif"
                                                        alt="Online Exam Logo"
                                                        style={{
                                                            height: "50px",
                                                            width: "auto",
                                                        }}
                                                    />
                                                    <h2 className="mb-0 quiz-content-ml">
                                                        {quiz.name}
                                                    </h2>
                                                </div>

                                                <button
                                                    className="terms-btn"
                                                    onClick={() => {
                                                        if (
                                                            rewardsRef.current
                                                        ) {
                                                            const top =
                                                                rewardsRef.current.getBoundingClientRect()
                                                                    .top +
                                                                window.scrollY;
                                                            const offset = 120; // header height or more
                                                            window.scrollTo({
                                                                top:
                                                                    top -
                                                                    offset,
                                                                behavior:
                                                                    "smooth",
                                                            });
                                                        }
                                                    }}
                                                >
                                                    Terms and Conditions
                                                </button>
                                            </div>

                                            <div className="d-flex justify-content-between">
                                                <div className="saasio-post-meta dates">
                                                    <a
                                                        href="#"
                                                        className="date"
                                                    >
                                                        <svg
                                                            className="svg-css"
                                                            id="Capa_1"
                                                            enableBackground="new 0 0 512.228 512.228"
                                                            height="13"
                                                            viewBox="0 0 512.228 512.228"
                                                            width="13"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <g>
                                                                <path d="m413.333 39.447h-19.106v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-196.227v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-19.105c-54.531 0-98.895 44.364-98.895 98.894v274.878c0 54.531 44.364 98.895 98.895 98.895h314.439c54.53 0 98.894-44.364 98.894-98.895v-274.878c0-54.53-44.364-98.894-98.895-98.894zm-314.438 40h19.105v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h196.228v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h19.106c32.474 0 58.894 26.42 58.894 58.894v19.106h-432.228v-19.106c0-32.474 26.42-58.894 58.895-58.894zm314.438 392.667h-314.438c-32.475 0-58.895-26.42-58.895-58.895v-215.772h432.228v215.772c0 32.475-26.42 58.895-58.895 58.895zm-235.666-196c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118 118c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20z" />
                                                            </g>
                                                        </svg>
                                                        Start Date:{" "}
                                                        <span className="start">
                                                            {" "}
                                                            {
                                                                quiz.start_date
                                                            }{" "}
                                                            {quiz.start_time}
                                                        </span>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        className="date"
                                                    >
                                                        <svg
                                                            className="svg-css"
                                                            id="Capa_1"
                                                            enableBackground="new 0 0 512.228 512.228"
                                                            height="13"
                                                            viewBox="0 0 512.228 512.228"
                                                            width="13"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <g>
                                                                <path d="m413.333 39.447h-19.106v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-196.227v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-19.105c-54.531 0-98.895 44.364-98.895 98.894v274.878c0 54.531 44.364 98.895 98.895 98.895h314.439c54.53 0 98.894-44.364 98.894-98.895v-274.878c0-54.53-44.364-98.894-98.895-98.894zm-314.438 40h19.105v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h196.228v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h19.106c32.474 0 58.894 26.42 58.894 58.894v19.106h-432.228v-19.106c0-32.474 26.42-58.894 58.895-58.894zm314.438 392.667h-314.438c-32.475 0-58.895-26.42-58.895-58.895v-215.772h432.228v215.772c0 32.475-26.42 58.895-58.895 58.895zm-235.666-196c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118 118c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20z" />
                                                            </g>
                                                        </svg>
                                                        End Date:{" "}
                                                        <span className="end">
                                                            {quiz.end_date}{" "}
                                                            {quiz.end_time}
                                                        </span>
                                                    </a>
                                                </div>

                                                <div className="blog-feed-share float-right">
                                                    <small className="me-1">
                                                        Share:
                                                    </small>
                                                    <a href="#">
                                                        <img
                                                            src="/design/assets/social/facebook.svg"
                                                            width="22"
                                                            alt=""
                                                        />
                                                    </a>
                                                    <a href="#">
                                                        <img
                                                            src="/design/assets/social/twitter.svg"
                                                            width="22"
                                                            alt=""
                                                        />
                                                    </a>
                                                    <a href="#">
                                                        <img
                                                            src="/design/assets/social/whatsapp.svg"
                                                            width="22"
                                                            alt=""
                                                        />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Image + Login/Play */}
                                        {/* <div className="blog-details-img wow fadeInLeft">
                    <div className="postr d-flex align-items-center justify-content-between flex-wrap">
                      <div className="col-lg-7 p-0">
                        <img
                          src="/design/assets/dd.png"
                          alt="Next-Gen GST Reforms Quiz"
                          className="img-fluid rounded"
                        />
                      </div>

                      {!showQuiz && (
                        <div className="col-lg-5 d-flex align-items-center justify-content-center">
                          <div className="it-nw-btn text-center">
                            <a
                              href="#"
                              className="d-flex part justify-content-center align-items-center"
                              onClick={handleProceed}
                            >
                              Login to Play Quiz
                            </a>
                          </div>
                        </div>
                      )}
                    </div>
                  </div> */}
                                        <div className="blog-details-img wow fadeInLeft">
                                            <div className="postr d-flex align-items-center justify-content-between flex-wrap">
                                                <div className="col-lg-7 p-0">
                                                    <img
                                                        src={
                                                            quiz.poster
                                                                ? `${quiz.poster}`
                                                                : "/design/assets/dd.png"
                                                        }
                                                        // src={"/design/assets/dd.png"}
                                                        alt={quiz.name}
                                                        className="img-fluid rounded"
                                                    />
                                                </div>
                                                {/* {!showQuiz && ( */}
                                                <div className="col-lg-5 d-flex flex-column align-items-center justify-content-center">
                                                    <div className="mb-3 d-flex gap-3">
                                                        <div className="quiz-stats text-center">
                                                            <div className="stat-number">
                                                                {
                                                                    quiz.total_questions
                                                                }
                                                            </div>
                                                            <div className="stat-label">
                                                                Questions
                                                            </div>
                                                        </div>
                                                        <div className="quiz-stats text-center">
                                                            <div className="stat-number">
                                                                {quiz.timer}
                                                            </div>
                                                            <div className="stat-label">
                                                                Seconds
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="it-nw-btn text-center">
                                                        <a
                                                            href="#"
                                                            className={`d-flex part justify-content-center align-items-center ${
                                                                hasCompleted
                                                                    ? "completed"
                                                                    : ""
                                                            }`}
                                                            onClick={
                                                                handleProceed
                                                            }
                                                            style={{
                                                                width: "260px",
                                                                backgroundColor:
                                                                    hasCompleted
                                                                        ? "#6c757d"
                                                                        : "",
                                                                cursor: hasCompleted
                                                                    ? "not-allowed"
                                                                    : "pointer",
                                                                opacity:
                                                                    hasCompleted
                                                                        ? 0.7
                                                                        : 1,
                                                            }}
                                                        >
                                                            {checkingCompletion
                                                                ? "Checking..."
                                                                : hasCompleted
                                                                ? "Already Completed ✓"
                                                                : isAuthenticated
                                                                ? "Play Quiz"
                                                                : "Login to Play Quiz"}
                                                        </a>
                                                    </div>
                                                </div>
                                                {/* )} */}
                                            </div>
                                        </div>

                                        {/* About Quiz */}
                                        <div className="about-quiz mt-4 black quiz-color quiz-content-ml">
                                            {/* <h3>About Quiz</h3> */}
                                            {/* <p>
                      ഗുഡ്‌സ് ആൻഡ് സർവീസ് ടാക്‌സ് (GST) 2017ൽ ആരംഭിച്ചതിന് ശേഷം
                      ഭരണഘടനയിലെ 101-ാമത് ഭേദഗതി വഴി പ്രാബല്യത്തിൽ വന്നു. ഈ
                      ക്വിസ് ജിഎസ്ടിയുടെ പരിഷ്കാരങ്ങളെ കുറിച്ച് ബോധവൽക്കരണം
                      സൃഷ്ടിക്കുന്നതിനും പൊതുജനങ്ങളെ ഉൾപ്പെടുത്തുന്നതിനുമാണ്
                      ലക്ഷ്യമിടുന്നത്.
                    </p>
                    <p>
                      ഈ ക്വിസിലൂടെ ജിഎസ്ടി സംബന്ധിച്ച പുതിയ
                      പരിഷ്കാരങ്ങളെക്കുറിച്ചുള്ള ധാരണയും ബോധവൽക്കരണവും
                      ശക്തിപ്പെടുത്തുകയാണ് ഉദ്ദേശം.
                    </p> */}
                                            {quiz.about}
                                        </div>

                                        {/* Rewards Section */}
                                        <div
                                            className="rewards mt-4 black quiz-color quiz-content-ml"
                                            ref={rewardsRef}
                                        >
                                            {/* <h4>മത്സരഫലം:</h4> */}
                                            {/* <ul>
                      <li>
                        🥇 ഒന്നാം സ്ഥാനത്തിന് <strong>₹5000</strong> രൂപ
                        സമ്മാനം.
                      </li>
                      <li>
                        🥈 രണ്ടാം സ്ഥാനത്തിന് <strong>₹2000</strong> രൂപ
                        സമ്മാനം.
                      </li>
                      <li>
                        🥉 മൂന്നാം സ്ഥാനത്തിന് <strong>₹1000</strong> രൂപ
                        സമ്മാനം.
                      </li>
                    </ul> */}
                                            <div>
                                                <h4>
                                                    {
                                                        quiz.result?.split(
                                                            "\r\n"
                                                        )[0]
                                                    }
                                                </h4>
                                                <ul>
                                                    {quiz.result
                                                        ?.split("\r\n")
                                                        .slice(2)
                                                        .map(
                                                            (item, index) =>
                                                                item.trim() && (
                                                                    <li
                                                                        key={
                                                                            index
                                                                        }
                                                                    >
                                                                        {item.trim()}
                                                                    </li>
                                                                )
                                                        )}
                                                </ul>
                                            </div>
                                        </div>

                                        {/* Terms and Conditions */}
                                        {/* <div className="terms mt-5 quiz-color quiz-content-ml">
                                            <h4>
                                                {
                                                    quiz.terms_condition?.split(
                                                        "\r\n"
                                                    )[0]
                                                }
                                            </h4>
                                            <ul>
                                                {quiz.terms_condition
                                                    ?.split("\r\n    " || "\r\n●\t")
                                                    // .slice(2)
                                                    .map(
                                                        (item, index) =>
                                                            item.trim() && (
                                                                <li key={index}>
                                                                    {item.trim()}
                                                                </li>
                                                            )
                                                    )}
                                            </ul>
                                        </div> */}

                                        <div className="terms mt-5 quiz-color quiz-content-ml">
                                            <h4>നിയമാവലി</h4>

                                            <ul>
                                                {quiz.terms_condition
                                                    ?.split("\r\n")
                                                    .map((item, index) => {
                                                        const cleanedItem = item
                                                            .replace("●", "●")
                                                            .trim();
                                                        return cleanedItem ? (
                                                            <li key={index}>
                                                                {cleanedItem}
                                                            </li>
                                                        ) : null;
                                                    })}
                                            </ul>
                                        </div>

                                        {/* Footer info */}
                                        {/* <div className="quiz-footer mt-5 text-center text-muted">
                   <button className=" proceed-btn" onClick={handleProceed}>
          Proceed
        </button>
                  </div> */}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </section>
        </>
    );
};

export default QuizDetails;
