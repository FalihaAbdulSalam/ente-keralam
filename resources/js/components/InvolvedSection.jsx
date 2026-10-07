import { useState, useEffect, useRef } from "react";
import { pledgeAPI, competitionAPI, pollsAPI, quizAPI, discussionsAPI } from "../services/api";
import { useLanguage } from "./LanguageContext";

const ServiceSection = () => {
  const { language } = useLanguage();
  const [activeTab, setActiveTab] = useState("quiz");
  const [pledgeData, setPledgeData] = useState([]);
  const [pledgeMessage, setPledgeMessage] = useState("");
  const [pledgesLoading, setPledgesLoading] = useState(true);
  const [pollsData, setPollsData] = useState([]);
  const [pollsMessage, setPollsMessage] = useState("");
  const [pollsLoading, setPollsLoading] = useState(true);
  const [quizzesData, setQuizzesData] = useState([]);
  const [quizzesLoading, setQuizzesLoading] = useState(true);
  const [quizMessage, setQuizMessage] = useState("");
  const [competitionsData, setCompetitionsData] = useState([]);
  const [competitionsLoading, setCompetitionsLoading] = useState(true);
  const [competitionsMessage, setCompetitionsMessage] = useState("");
  const [discussionsData, setDiscussionsData] = useState([]);
  const [discussionsLoading, setDiscussionsLoading] = useState(true);
  const [discussionsMessage, setDiscussionsMessage] = useState("");

  // Scroll target for tab content/cards
  const cardsSectionRef = useRef(null);

  // Helper to get display text based on language
  const getDisplayText = (enText, malText) => {
    return language === 'ml' ? (malText || enText) : enText;
  };

  // Fetch pledge data from API
  useEffect(() => {
    const fetchPledges = async () => {
      try {
        setPledgesLoading(true);
        setPledgeMessage("");
        const response = await pledgeAPI.getAll();

        // Handle error response from backend (result: false)
        if (response?.result === 0) {
          setPledgeMessage(response.message || "No active pledges found.");
          setPledgeData([]);
        } else if (response?.status === 0) {
          // Fallback for status field
          setPledgeMessage(response.message || "No active pledges found.");
          setPledgeData([]);
        } else if (response?.data) {
          // Handle different response structures
          const pledges = response.data.pledges || response.data.data || response.data || [];
          const pledgeArray = Array.isArray(pledges) ? pledges : (pledges ? [pledges] : []);
          if (pledgeArray.length > 0) {
            setPledgeData(pledgeArray);
          } else {
            setPledgeMessage("No active pledges available.");
            setPledgeData([]);
          }
        } else {
          setPledgeMessage("No active pledges available.");
          setPledgeData([]);
        }
      } catch (error) {
        setPledgeMessage("No active pledges found.");
        setPledgeData([]);
      } finally {
        setPledgesLoading(false);
      }
    };

    fetchPledges();
  }, []);

  // Fetch polls from API
  useEffect(() => {
    const fetchPolls = async () => {
      try {
        setPollsLoading(true);
        setPollsMessage("");
        // const response = await fetch("/api/allpolldata");
        const response = await pollsAPI.getAll();
        // const json = await response.json();
        const json = response.data;
        if (json?.status === false) {
          // Handle error response from backend
          setPollsMessage(json.message || "No active polls found.");
          setPollsData([]);
        } else if (json?.data && Array.isArray(json.data)) {
          if (json.data.length > 0) {
            setPollsData(json.data);
          } else {
            setPollsMessage("No active polls available.");
            setPollsData([]);
          }
        } else {
          setPollsMessage("No active polls available.");
          setPollsData([]);
        }
      } catch (error) {
        console.error("Error fetching polls:", error);
        setPollsMessage("Failed to load polls. Please try again later.");
        setPollsData([]);
      } finally {
        setPollsLoading(false);
      }
    };

    if (activeTab === "tasks") {
      fetchPolls();
    }
  }, [activeTab]);

  // Fetch contests from API when competitions tab is active
  useEffect(() => {
    const fetchContests = async () => {
      try {
        setCompetitionsLoading(true);
        setCompetitionsMessage("");
        const response = await competitionAPI.getAll();

        if (response?.status === false) {
          // Handle error response from backend
          setCompetitionsMessage(response.message || "No active competitions found.");
          setCompetitionsData([]);
        } else {
          // response may be { result, message, data: [...] } or direct data
          const contestPayload = response.data?.data ?? response.data ?? [];
          const contestArray = Array.isArray(contestPayload) ? contestPayload : (contestPayload ? [contestPayload] : []);

          if (contestArray.length > 0) {
            setCompetitionsData(contestArray);
          } else {
            setCompetitionsMessage("No active competitions available.");
            setCompetitionsData([]);
          }
        }
      } catch (error) {
        console.error("Error fetching contests:", error);
        setCompetitionsMessage("Failed to load competitions. Please try again later.");
        setCompetitionsData([]);
      } finally {
        setCompetitionsLoading(false);
      }
    };

    if (activeTab === "compet") {
      fetchContests();
    }
  }, [activeTab]);

  // Fetch quizzes from API
  useEffect(() => {
    const fetchQuizzes = async () => {
      try {
        setQuizzesLoading(true);
        setQuizMessage(""); // reset message before fetching
        // const response = await fetch("/api/allquizdata");
        const response = await quizAPI.getAll();
        const json = response.data;

        if (json?.status === false) {
          // 👇 show message from backend
          setQuizMessage(json.message || "No active quizzes found.");
          setQuizzesData([]);
        } else if (json?.data && Array.isArray(json.data)) {
          setQuizzesData(json.data);
        } else {
          setQuizMessage("No active quizzes available.");
          setQuizzesData([]);
        }
      } catch (error) {
        console.error("Error fetching quizzes:", error);
        setQuizMessage("Failed to load quizzes. Please try again later.");
        setQuizzesData([]);
      } finally {
        setQuizzesLoading(false);
      }
    };

    if (activeTab === "quiz") {
      fetchQuizzes();
    }
  }, [activeTab]);

  // Fetch discussions from API
  useEffect(() => {
    const fetchDiscussions = async () => {
      try {
        setDiscussionsLoading(true);
        setDiscussionsMessage("");
        const response = await discussionsAPI.getAll();
        
        if (response?.data?.status === false) {
          setDiscussionsMessage(response.data.message || "No active discussions found.");
          setDiscussionsData([]);
        } else if (response?.data?.data && Array.isArray(response.data.data)) {
          if (response.data.data.length > 0) {
            setDiscussionsData(response.data.data);
          } else {
            setDiscussionsMessage("No active discussions available.");
            setDiscussionsData([]);
          }
        } else {
          setDiscussionsMessage("No active discussions available.");
          setDiscussionsData([]);
        }
      } catch (error) {
        console.error("Error fetching discussions:", error);
        setDiscussionsMessage("Failed to load discussions. Please try again later.");
        setDiscussionsData([]);
      } finally {
        setDiscussionsLoading(false);
      }
    };

    if (activeTab === "disscus") {
      fetchDiscussions();
    }
  }, [activeTab]);

  // Tabs configuration
  const tabs = [
    { id: "quiz", img: "qs.svg", image: "qs25.svg", title: "Quizzes" },
    { id: "compet", img: "compe.svg", image: "comp25.svg", title: "Competitions" },
    { id: "tasks", img: "pollbox.svg", image: "poll25.svg", title: "Polls/Survey" },
    { id: "pledge", img: "pled.svg", image: "pledge25.svg", title: "Pledges" },
    { id: "todo", img: "todoo.svg", image: "task25.svg", title: "To-do Tasks" },
    { id: "disscus", img: "diss.svg", image: "gd25.svg", title: "Discussion" },
  ];

  // 🖼️ Different image sets for each section
  const sectionImages = {
    tasks: ["Poll - 1.jpg", "Poll - 2.jpg", "Poll - 3.jpg", "Poll - 4.jpg"],
    quiz: ["1_നവകേരളം ക്വിസ് 2025.jpg", "2_അതിദരിദ്രരില്ലാത്ത കേരളം  - ക്വിസ്.jpg", "3_ഡിജിറ്റൽ കേരളം ക്വിസ്.jpg", "4_കുടുംബശ്രീ ക്വിസ്.jpg"],
    disscus: ["Pledge - 1.jpg", "Pledge - 2.jpg", "Pledge - 3.jpg", "Pledge - 4.jpg"],
    todo: ["Pledge - 1.jpg", "Pledge - 2.jpg", "Pledge - 3.jpg", "Pledge - 4.jpg"],
    blogs: ["Pledge - 1.jpg", "Pledge - 2.jpg", "Pledge - 3.jpg", "Pledge - 4.jpg"],
    compet: ["Competition - 1.jpg", "Competition - 2.jpg", "Competition - 3.jpg", "Competition - 4.jpg"],
  };

  // 🧩 Helper to render section cards dynamically
  // 🧩 Helper to render section cards dynamically
  const renderCards = (tabId, link = "", idStart = 1) => {
    // Special handling for pledge section - use API data
    if (tabId === "pledge") {
      return (
        <div className="row">
          {pledgesLoading ? (
            <p className="text-center w-100">Loading pledges...</p>
          ) : pledgeMessage ? (
            <p className="text-center w-100">{pledgeMessage}</p>
          ) : pledgeData.length > 0 ? (
            pledgeData.slice(0, 4).map((pledge, index) => (
              <div
                className="col-lg-3 col-md-3 col-xl-3 col-6 padx"
                key={pledge.id || pledge.pledge_id || index}
              >
                <div className="invo-card">
                  <div className="image-container">
                    <img
                      src={pledge.poster || `/design/assets/fp/Pledge - ${index + 1}.jpg`}
                      alt={pledge.title || 'Pledge'}
                    />
                    <div className="overlay">
                      <div
                        className="v-all it-nw-btn text-center mt-3 wow flipInX"
                        data-wow-delay="200ms"
                        data-wow-duration="1500ms"
                      >
                        <a
                          className="d-flex justify-content-center align-items-center"
                          href={`/pledge/${pledge.id || pledge.pledge_id || index + 1}`}
                        >
                          Participate Now
                        </a>
                      </div>
                    </div>
                  </div>
                  <h6>{pledge.title || 'Let\'s take part in this and be a changemaker'}</h6>
                  <div className="apldg-blog-meta1">
                    <span className="apldg-blog-date">
                      Last date : {pledge.end_date || pledge.ending_date || 'July 5 2025'}
                    </span>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="text-center w-100">No pledges available right now.</p>
          )}
        </div>
      );
    }

    // Polls tab - uses API data
    if (tabId === "tasks") {
      if (pollsLoading) {
        return <div className="text-center py-4">Loading polls...</div>;
      }

      return (
        <div className="row">
          {pollsMessage ? (
            <p className="text-center w-100">{pollsMessage}</p>
          ) : (pollsData || []).length > 0 ? (
            (pollsData || []).slice(0, 4).map((poll, index) => (
              <div className="col-lg-3 col-md-3 col-xl-3 col-6 padx" key={poll.poll_id || index}>
                <div className="invo-card">
                  <div className="image-container">
                    <img
                      src={poll.poster ? `${poll.poster}` : `/design/assets/fp/Poll - ${index + 1}.jpg`}
                      alt={poll.name}
                    />
                    <div className="overlay">
                      <div className="v-all it-nw-btn text-center mt-3 wow flipInX" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <a className="d-flex justify-content-center align-items-center" href={`/poll-details/${poll.poll_id}`}>
                          Participate Now
                        </a>
                      </div>
                    </div>
                  </div>
                  <h6>{poll.topic || poll.name || "Let's take part in this and be a changemaker"}</h6>
                  <div className="apldg-blog-meta1">
                    <span className="apldg-blog-date">Last date: {poll.end_date || "Coming Soon"}</span>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="text-center w-100">No polls available right now.</p>
          )}
        </div>
      );
    }

    // Quizzes tab - uses API data
    if (tabId === "quiz") {
      if (quizzesLoading) {
        return <div className="text-center py-4">Loading quizzes...</div>;
      }

      return (
        <div className="row">
          {quizzesLoading ? (
            <p className="text-center w-100">Loading quizzes...</p>
          ) : quizMessage ? (
            <p className="text-center w-100">{quizMessage}</p>
          ) : (quizzesData || []).length > 0 ? (
            (quizzesData || []).slice(0, 4).map((quiz, index) => (
              <div className="col-lg-3 col-md-3 col-xl-3 col-6 padx" key={quiz.quiz_id || index}>
                <div className="invo-card">
                  <div className="image-container">
                    <img
                      src={
                        quiz.poster
                          ? `${quiz.poster}`
                          : `/design/assets/fp/${sectionImages.quiz[index]}`
                      }
                      alt={quiz.name}
                    />
                    <div className="overlay">
                      <div
                        className="v-all it-nw-btn text-center mt-3 wow flipInX"
                        data-wow-delay="200ms"
                        data-wow-duration="1500ms"
                      >
                        <a
                          className="d-flex justify-content-center align-items-center"
                          href={`/quiz-details/${quiz.quiz_id}`}
                        >
                          Participate Now
                        </a>
                      </div>
                    </div>
                  </div>
                  <h6>{quiz.topic || quiz.name || "Let's take part in this and be a changemaker"}</h6>
                  <div className="apldg-blog-meta1">
                    <span className="apldg-blog-date">
                      Last date: {quiz.end_date || "Coming Soon"}
                    </span>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="text-center w-100">No quizzes available right now.</p>
          )}
        </div>
      );
    }

    // Discussions tab - uses API data
    if (tabId === "disscus") {
      if (discussionsLoading) {
        return <div className="text-center py-4">Loading discussions...</div>;
      }

      return (
        <div className="row">
          {discussionsMessage ? (
            <p className="text-center w-100">{discussionsMessage}</p>
          ) : (discussionsData || []).length > 0 ? (
            (discussionsData || []).slice(0, 4).map((discussion, index) => (
              <div className="col-lg-3 col-md-3 col-xl-3 col-6 padx" key={discussion.discussion_id || index}>
                <div className="invo-card">
                  <div className="image-container">
                    <img
                      src={discussion.banner || discussion.poster || `/design/assets/fp/${sectionImages.disscus[index % 4]}`}
                      alt={discussion.discussion_title}
                    />
                    <div className="overlay">
                      <div className="v-all it-nw-btn text-center mt-3 wow flipInX" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <a className="d-flex justify-content-center align-items-center" href={`/discussion/${discussion.discussion_id}`}>
                          Participate Now
                        </a>
                      </div>
                    </div>
                  </div>
                  <h6>{discussion.discussion_title || "Join this discussion and share your thoughts"}</h6>
                  <div className="apldg-blog-meta1">
                    <span className="apldg-blog-date">Last date: {discussion.discussion_endDate || "Coming Soon"}</span>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="text-center w-100">No discussions available right now.</p>
          )}
        </div>
      );
    }

    // Competitions tab - use contests from API
    if (tabId === "compet") {
      if (competitionsLoading) {
        return <div className="text-center py-4">Loading competitions...</div>;
      }

      return (
        <div className="row">
          {competitionsMessage ? (
            <p className="text-center w-100">{competitionsMessage}</p>
          ) : (competitionsData || []).length > 0 ? (
            (competitionsData || []).slice(0, 4).map((contest, index) => (
              <div className="col-lg-3 col-md-3 col-xl-3 col-6 padx" key={contest.contest_id || index}>
                <div className="invo-card">
                  <div className="image-container">
                    <img
                      src={contest.poster || `/design/assets/fp/${sectionImages.compet[index]}`}
                      alt={contest.title}
                    />
                    <div className="overlay">
                      <div className="v-all it-nw-btn text-center mt-3 wow flipInX" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <a className="d-flex justify-content-center align-items-center" href={`/competition-details/${contest.slug}`}>
                          Participate Now
                        </a>
                      </div>
                    </div>
                  </div>
                  <h6>{contest.title || "Let's take part in this and be a changemaker"}</h6>
                  <div className="apldg-blog-meta1">
                    <span className="apldg-blog-date">Last date: {contest.end_date || "Coming Soon"}</span>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="text-center w-100">No competitions available right now.</p>
          )}
        </div>
      );
    }

    // Default rendering for other tabs
    const activityName = tabs.find(tab => tab.id === tabId)?.title || "activities";
    return (
      <div className="row">
        <p className="text-center w-100">No {activityName} available right now.</p>
      </div>
    );
  };

  return (
    <section id="it-nw-service" className="it-nw-service-section position-relative">
      <div className="it-nw-service-sh1 position-absolute">
        <img src="/design/assets/ker.png" alt="" />
      </div>
      <div className="it-nw-side-bg text-center position-absolute">
        <img src="/design/assets/background/Component 574.svg" alt="" />
      </div>

      <div className="container">
        <div className="it-nw-service-upper-wrapper" ref={cardsSectionRef}>
          <div className="row">
            <div className="col-lg-4 col-md-4 col-12 wow fadeInLeft" data-wow-delay="0ms"
              data-wow-duration="1500ms">
              <div className="it-nw-section-title headline pera-content">
                <span className="it-nw-title-tag">Participate & Contribute</span>
                <h2>Get Involved</h2>
              </div>
            </div>

            {/* Tabs */}
            <div className="col-lg-12 wow fadeInRight d-nne" data-wow-delay="200ms"
              data-wow-duration="1500ms">
              <div className="it-nw-service-content mt-2 pt-2">
                <div className="taskz1">
                  {tabs.map((tab) => (
                    <div className="tasks" key={tab.id}>
                      <a
                        href="#"
                        id={tab.id}
                        className={`tab-link ${activeTab === tab.id ? "active" : ""}`}
                        onClick={(e) => {
                          e.preventDefault(); // Prevent page jump
                          setActiveTab(tab.id);
                          // Smooth scroll to cards/content section
                          if (cardsSectionRef.current) {
                            const top = cardsSectionRef.current.getBoundingClientRect().top + window.scrollY - 80;
                            window.scrollTo({ top, behavior: "smooth" });
                          }
                        }}
                      >
                        <div className="it-nw-service-innerbox position-relative">
                          <div className="Hico">
                            <img src={`/design/assets/new/${tab.image}`} alt="" className="svg-icon" />
                          </div>
                          <div className="it-nw-service-inner-text headline pera-content">
                            <h3>{tab.title.toUpperCase()}</h3>
                          </div>
                        </div>
                      </a>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {/* ================= Tabs Content ================= */}
          {activeTab === "tasks" && (
            <div className="tasks-tab pr-mark-testimonial-content mt-3">
              <h5>Polls / Survey</h5>
              <h6 className="hh6">Participate in our latest surveys and make your voice heard!</h6>
              {renderCards("tasks", "/poll-details")}
              {!pollsLoading && !pollsMessage && pollsData.length > 0 && (
                <div className="it-nw-btn text-center mt-4 text-center  position-relative">
                  <a
                    className="d-flex justify-content-center align-items-center"
                    href="/polls"
                  >
                    View All
                  </a>
                </div>
              )}
            </div>
          )}

          {activeTab === "quiz" && (
            <div className="quiz-tab pr-mark-testimonial-content">
              <h4>Quiz</h4>
              <h6 className="hh6">Test your knowledge with our fun and informative quizzes.</h6>
              {renderCards("quiz", "/quiz-details")}
              {!quizzesLoading && !quizMessage && quizzesData.length > 0 && (
                <div className="it-nw-btn text-center mt-4 text-center  position-relative">
                  <a
                    className="d-flex justify-content-center align-items-center"
                    href="/quiz-list"
                  >
                    View All
                  </a>
                </div>
              )}
            </div>
          )}

          {activeTab === "disscus" && (
            <div className="discuss-tab pr-mark-testimonial-content">
              <h4>Discussion</h4>
              <h6 className="hh6">Join discussions and share your thoughts on trending topics.</h6>
              {renderCards("disscus", "/discussion")}
              {!discussionsLoading && !discussionsMessage && discussionsData.length > 0 && (
                <div className="it-nw-btn text-center mt-4 text-center  position-relative">
                  <a
                    className="d-flex justify-content-center align-items-center"
                    href="/discussion"
                  >
                    View All
                  </a>
                </div>
              )}
            </div>
          )}

          {activeTab === "todo" && (
            <div className="todo-tab pr-mark-testimonial-content">
              <h4>To-do Tasks</h4>
              <h6 className="hh6">Complete daily challenges and become a changemaker!</h6>
              {renderCards("todo", "/")}
            </div>
          )}

          {activeTab === "pledge" && (
            <div className="pledge-tab pr-mark-testimonial-content">
              <h4>Pledge</h4>
              <h6 className="hh6">Take a pledge and commit to positive change.</h6>
              {renderCards("pledge", "/pledge")}
              {!pledgesLoading && !pledgeMessage && pledgeData.length > 0 && (
                <div className="it-nw-btn text-center mt-4 text-center  position-relative">
                  <a
                    className="d-flex justify-content-center align-items-center"
                    href="/pledge-list"
                  >
                    View All
                  </a>
                </div>
              )}
            </div>
          )}

          {activeTab === "compet" && (
            <div className="competitions-tab pr-mark-testimonial-content">
              <h4>Competitions</h4>
              <h6 className="hh6">Compete and showcase your skills to the world.</h6>
              {renderCards("compet", "/competition-details")}
              {!competitionsLoading && !competitionsMessage && competitionsData.length > 0 && (
                <div className="it-nw-btn text-center mt-4 text-center position-relative">
                  <a
                    className="d-flex justify-content-center align-items-center"
                    href="/competition-list"
                  >
                    View All
                  </a>
                </div>
              )}
            </div>
          )}
        </div>
      </div>
    </section>
  );
};

export default ServiceSection;

