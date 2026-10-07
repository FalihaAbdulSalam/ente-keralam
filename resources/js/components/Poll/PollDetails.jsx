"use client";
import { useEffect, useRef, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useAuth } from "../App";
import { activityAPI, pollsAPI } from "../../services/api";
import MorphingPreloader from "../MorphingPreloader";
import { jsPDF } from "jspdf";
import "../DiscussionBreadcrumb.css"; // ✅ Import external CSS

const PollDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { isAuthenticated, user } = useAuth();
  const [showPoll, setShowPoll] = useState(false);
  const [showPopup, setShowPopup] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [answers, setAnswers] = useState({});
  const [pollData, setPollData] = useState(null);
  const [votes, setVotes] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [hasCompleted, setHasCompleted] = useState(false);
  const [checkingCompletion, setCheckingCompletion] = useState(true);
  const [completionMessageVisible, setCompletionMessageVisible] = useState(false);
  const [showCertificateModal, setShowCertificateModal] = useState(false);
  const [isGeneratingCert, setIsGeneratingCert] = useState(false);
  const [savingResponse, setSavingResponse] = useState(false);

  const pollSectionRef = useRef(null);
  const autoSaveTimeoutRef = useRef(null);
 const shapes = [
        "Vector (1).svg",
        "Vector (2).svg",
        // "Vector (5).svg",
        // "Vector (3).svg",
        "Vector (4).svg",
    ];
    const shapeSizes = {
        "Vector (1).svg": 10,
        "Vector (2).svg": 14,
        // "Vector (3).svg": 8,
        "Vector (4).svg": 16,
        // "Vector (5).svg": 12,
    };

  // Map route id to API id if backend expects different ids for the same page id
  const mapParamToApiId = (paramId) => {
    if (!paramId) return "1"; // default
    return String(paramId);
  };

  // Fetch poll data when id changes
  useEffect(() => {
    const fetchPoll = async () => {
      setLoading(true);
      try {
        const apiId = mapParamToApiId(id);
        const url = `/api/polldata/${apiId}`;
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        
        // Handle API response structure {status: true, poll: {...}}
        const pollInfo = data.poll || data;
        setPollData(pollInfo);

        // initialize votes aligned with questions order
        const initialVotes = (pollInfo.questions || []).map((q) => ({
          questionId: q.question_id,
          counts: Array((q.options || []).length).fill(0),
          total: 0,
        }));
        setVotes(initialVotes);
      } catch (err) {
        console.error("Failed to fetch poll data:", err);
        setError(err.message || "Failed to fetch");
      } finally {
        setLoading(false);
      }
    };
    fetchPoll();
  }, [id]);

  // Check if user has already completed this poll
  useEffect(() => {
    if (isAuthenticated && id) {
      checkCompletion();
    } else {
      setCheckingCompletion(false);
    }
  }, [isAuthenticated, id]);

  const checkCompletion = async () => {
    try {
      const response = await activityAPI.checkCompletion('poll', id);
      if (response.data.success) {
        const completed = response.data.data.has_completed;
        setHasCompleted(completed);
        if (completed) {
          setCompletionMessageVisible(true);
        }
      }
    } catch (error) {
      console.error("Error checking completion:", error);
    } finally {
      setCheckingCompletion(false);
    }
  };

  // Submit poll response to API
  const submitPollResponse = async (answersToSubmit, isFinalSubmission = false) => {
    if (!pollData || !user || !isAuthenticated) return;

    const userId = user.user_id || user.id;
    const pollId = pollData.poll_id || pollData.id || id;
    
    if (!userId || !pollId) {
      console.error("Missing user_id or poll_id");
      return;
    }

    const questions = pollData.questions || [];
    const pollAnswers = [];

    // Build poll_answers array from provided answers
    Object.keys(answersToSubmit).forEach((questionId) => {
      const optionIndex = answersToSubmit[questionId];
      const question = questions.find((q) => q.question_id === Number(questionId));
      if (question && question.options && question.options[optionIndex]) {
        const selectedOption = question.options[optionIndex];
        const optionId = selectedOption.option_id;
        if (optionId !== undefined && optionId !== null) {
          pollAnswers.push({
            question_id: Number(questionId),
            selected_option_id: Number(optionId),
          });
        }
      }
    });

    // If no answers yet, don't submit
    if (pollAnswers.length === 0 && !isFinalSubmission) {
      return;
    }

    try {
      setSavingResponse(true);
      const responseData = {
        user: {
          user_id: Number(userId),
        },
        poll: {
          poll_id: Number(pollId),
        },
        poll_answers: pollAnswers,
        is_final_submission: isFinalSubmission,
      };

      await pollsAPI.submitResponse(responseData);
      
      if (isFinalSubmission) {
        // Record activity and award points
        try {
          await pollsAPI.submitResponse(responseData);
        } catch (activityError) {
          console.error('Failed to record activity:', activityError);
        }
      }
    } catch (error) {
      console.error("Error submitting poll response:", error);
      // Don't show error for auto-save, only for final submission
      if (isFinalSubmission) {
        alert(error.response?.data?.message || "Failed to submit poll. Please try again.");
      }
    } finally {
      setSavingResponse(false);
    }
  };

  const handleChange = async (questionId, optionIndex) => {
    const newAnswers = { ...answers, [questionId]: optionIndex };
    setAnswers(newAnswers);
    
    // Auto-save response when user selects an answer
    if (isAuthenticated && user && pollData) {
      // Clear any pending auto-save
      if (autoSaveTimeoutRef.current) {
        clearTimeout(autoSaveTimeoutRef.current);
      }
      
      // Debounce auto-save with 500ms delay
      autoSaveTimeoutRef.current = setTimeout(() => {
        submitPollResponse(newAnswers, false);
        autoSaveTimeoutRef.current = null;
      }, 500);
    }
  };

  const handleSubmit = async () => {
    if (!pollData) return;
    const questions = pollData.questions || [];
    if (Object.keys(answers).length !== questions.length) {
      alert("Please answer all polls before submitting.");
      return;
    }

    // Clear any pending auto-save before final submission
    if (autoSaveTimeoutRef.current) {
      clearTimeout(autoSaveTimeoutRef.current);
      autoSaveTimeoutRef.current = null;
    }

    // Submit final response to API
    await submitPollResponse(answers, true);

    // Update local votes state
    const newVotes = votes.map((v) => {
      const qid = v.questionId;
      const answerIndex = answers[qid];
      if (answerIndex !== undefined) {
        const updatedCounts = [...v.counts];
        updatedCounts[answerIndex] = (updatedCounts[answerIndex] || 0) + 1;
        return { ...v, counts: updatedCounts, total: v.total + 1 };
      }
      return v;
    });

    setVotes(newVotes);
    setShowPopup(true);
    setTimeout(() => {
      setShowPopup(false);
      setSubmitted(true);
    }, 10000);
  };

  const handleClose = () => {
    setShowPopup(false);
    setSubmitted(true);
  };

  useEffect(() => {
    if (showPoll && pollSectionRef.current) {
      pollSectionRef.current.scrollIntoView({ behavior: "smooth" });
    }
  }, [showPoll]);

  // Cleanup timeout on unmount
  useEffect(() => {
    return () => {
      if (autoSaveTimeoutRef.current) {
        clearTimeout(autoSaveTimeoutRef.current);
      }
    };
  }, []);

  const handleParticipate = (e) => {
    e.preventDefault();
    if (isAuthenticated) {
      setShowPoll(true);
    } else {
      // Store current poll details page to redirect after login
      navigate('/login', { state: { from: `/poll-details/${id || 1}` } });
    }
  };

  const arrayBufferToBase64 = (buffer) => {
    let binary = "";
    const bytes = new Uint8Array(buffer);
    const chunkSize = 0x8000;
    for (let i = 0; i < bytes.length; i += chunkSize) {
      binary += String.fromCharCode.apply(
        null,
        Array.from(bytes.subarray(i, i + chunkSize))
      );
    }
    return window.btoa(binary);
  };

  const drawMalayalamTextAsImage = async (text, color = "#000000") => {
    const notoMalayalamFontUrl = "/font/NotoSansMalayalam.ttf";
    try {
      const font = new FontFace(
        "Noto Sans Malayalam",
        `url(${notoMalayalamFontUrl})`
      );
      await font.load();
      document.fonts.add(font);
    } catch (e) {
      console.warn("Could not load Noto Sans Malayalam font:", e);
    }

    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    ctx.font = "50px 'Noto Sans Malayalam', sans-serif";
    const textWidth = ctx.measureText(text).width;

    canvas.width = textWidth + 20;
    canvas.height = 100;

    ctx.font = "40px 'Noto Sans Malayalam', sans-serif";
    ctx.fillStyle = color;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(text, canvas.width / 2, canvas.height / 2);

    return { dataUrl: canvas.toDataURL("image/png"), width: textWidth };
  };

  const handleDownloadCertificate = async () => {
    setIsGeneratingCert(true);
    try {
      const doc = new jsPDF({
        orientation: "landscape",
        unit: "px",
        format: [1200, 800],
      });

      const notoMalayalamFontUrl = "/font/NotoSansMalayalam.ttf";
      const openSansFontUrl = "/font/OpenSans.ttf";
      const meaFontUrl = "/font/MeaCulpa.ttf";
      const bgUrl = "/design/assets/certificate/certifiacte-participation.jpg";

      const [notoMalayalamBuf, meaBuf, openSansBuf, img] = await Promise.all([
        fetch(notoMalayalamFontUrl)
          .then((r) => (r.ok ? r.arrayBuffer() : Promise.resolve(null)))
          .catch(() => null),
        fetch(meaFontUrl)
          .then((r) => (r.ok ? r.arrayBuffer() : Promise.resolve(null)))
          .catch(() => null),
        fetch(openSansFontUrl)
          .then((r) => (r.ok ? r.arrayBuffer() : Promise.resolve(null)))
          .catch(() => null),
        new Promise((resolve, reject) => {
          const image = new Image();
          image.crossOrigin = "anonymous";
          image.onload = () => resolve(image);
          image.onerror = reject;
          image.src = bgUrl;
        }),
      ]);

      if (meaBuf) {
        const meaB64 = arrayBufferToBase64(meaBuf);
        doc.addFileToVFS("MeaCulpa.ttf", meaB64);
        doc.addFont("MeaCulpa.ttf", "MeaCulpa", "normal");
      }

      if (openSansBuf) {
        const openSansB64 = arrayBufferToBase64(openSansBuf);
        doc.addFileToVFS("OpenSans.ttf", openSansB64);
        doc.addFont("OpenSans.ttf", "OpenSans", "normal");
      }

      const pageW = doc.internal.pageSize.getWidth();
      const pageH = doc.internal.pageSize.getHeight();

      const canvas = document.createElement("canvas");
      canvas.width = img.width;
      canvas.height = img.height;
      const ctx = canvas.getContext("2d");
      ctx.drawImage(img, 0, 0);
      const bgDataUrl = canvas.toDataURL("image/jpeg");
      doc.addImage(bgDataUrl, "JPEG", 0, 0, pageW, pageH);

      const eventName = pollData?.topic || "Poll";
      const userName = user?.name || "Participant";

      const isMalayalam = /[\u0D00-\u0D7F]/.test(eventName);

      if (isMalayalam) {
        const { dataUrl, width } = await drawMalayalamTextAsImage(eventName);
        const displayWidth = Math.max(width, 300);
        const x = pageW / 2 - displayWidth / 2;
        const y = pageH * 0.75;
        doc.addImage(dataUrl, "PNG", x, y - 30, displayWidth, 60);
      } else {
        try {
          doc.setFont("OpenSans");
        } catch {
          doc.setFont("helvetica", "normal");
        }
        doc.setFontSize(40);
        doc.text(eventName, pageW / 2, pageH * 0.75, { align: "center" });
      }

      try {
        doc.setFont("MeaCulpa");
      } catch {
        doc.setFont("helvetica", "bold");
      }
      doc.setFontSize(76);
      doc.text(userName, pageW / 2, pageH * 0.6, { align: "center" });

      const safeName = (userName || "participant").replace(/[^a-z0-9_-]/gi, "_");
      const safeTitle = (eventName || "poll").replace(/[^a-z0-9_-]/gi, "_");
      doc.save(`${safeName}_${safeTitle}_certificate.pdf`);

      setShowCertificateModal(false);
    } catch (err) {
      console.error("Failed to generate certificate", err);
      alert("Could not generate certificate. Please try again.");
    } finally {
      setIsGeneratingCert(false);
    }
  };

console.log(pollData,"pollData");

  return (
    <>
      {/* Breadcrumb Section */}
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="container">
          <div className="breadcurmb-title">
            <h2>Poll</h2>
          </div>
          <div className="breadcurmb-item-list ul-li">
            <ul className="saasio-page-breadcurmb">
              <li>
                <a href="/">Home</a>
              </li>
              <li>
                <a href="/polls">Poll</a>
              </li>
              <li>
                <a href="#">Poll Details</a>
              </li>
            </ul>
          </div>
        </div>
      </section>

      {/* Poll Section */}
      <section id="news-feed" className="news-feed-section noto-font position-relative">
        {/* <div className="it-nw-side-bg text-center position-absolute">
          <img src="/design/assets/its-2/side-line.png" alt="" />
        </div> */}

        <div className="blog-shapes-grid-right">
                        {Array.from({ length: 40 }).map((_, i) => {
                            const file = shapes[i % shapes.length];
                            return (
                                <div
                                    className={`it-nw-blog-sh-bg sh${
                                        (i % 3) + 5
                                    }`}
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
          <div className="blog-feed-content pollDet">
            <div className="row">
              <div className="col-md-12">
                <div className="saasio-blog-details-content">
                  {/* Heading & meta */}
                  <div className="blog-details-text dia-headline wow fadeInTop">
                    <h2>{pollData?.topic}</h2>
                    <div className="d-flex justify-content-between">
                      <div className="saasio-post-meta dates">
                        <a href="#" className="date">
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
                          Start Date :
                          <span className="start">{pollData?.start_date || "-"}</span>
                        </a>
                        <a href="#"  className="date">
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
                          End Date :
                          <span className="end">{pollData?.end_date || "-"}</span>
                        </a>
                      </div>
                      <div className="blog-feed-share float-right">
                        <small className="me-1">Share:</small>
                        <a href="#">
                          <img
                            src="/design/assets/social/facebook.svg"
                            width="22"
                            alt=""
                          />
                        </a>
                        <a href="#">
                          <img src="/design/assets/social/insta.svg" width="22" alt="" />
                        </a>
                        <a href="#">
                          <img
                            src="/design/assets/social/whatsapp.svg"
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
                      </div>
                    </div>
                  </div>

                  {/* Main Image & Button */}
                  <div
                    className="blog-details-img wow fadeInLeft"
                    data-wow-delay="200ms"
                    data-wow-duration="1500ms"
                  >
                    <div className="postr">
                      <div className="d-flex">
                        <div className="col-lg-7 p-0">
                          <img src={pollData?.banner} alt="" />
                        </div>
                        {!showPoll && !completionMessageVisible && (
                          <div className="col-lg-5 p-0 d-flex align-items-center justify-content-center">
                            <div
                              className="it-nw-btn text-center wow fadeInRight"
                              data-wow-delay="200ms"
                              data-wow-duration="1500ms"
                            >
                              <a
                                className="d-flex part justify-content-center align-items-center"
                                href="#"
                                onClick={handleParticipate}
                              >Participate</a>
                            </div>
                          </div>
                        )}
                        {completionMessageVisible && (
                          <div className="col-lg-5 p-0 d-flex align-items-center justify-content-center">
                            <div className="it-nw-btn text-center" style={{ textAlign: 'center' }}>
                              <p style={{ color: '#2e7d32', marginBottom: '12px', fontSize: '14px' }}>✅ Poll Completed</p>
                              {/* <button
                                className="btn btn-primary btn-sm"
                                onClick={() => setShowCertificateModal(true)}
                                style={{ marginBottom: '8px' }}
                              >
                                Download Certificate
                              </button> */}
                            </div>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>

                  {/* Description (from API if available) */}
                  <div className="blog-details-text dia-headline wow fadeInRight noto-font">
                    <article>{pollData?.about || ""}</article>
                  </div>

                  {/* Polls */}
                  {showPoll && (
                    <div ref={pollSectionRef}>
                      <hr />
                      <div className="mt-4">
                        {loading && <MorphingPreloader type="poll" />}
                        {error && <p className="text-danger">Error: {error}</p>}

                        {!loading && !error && pollData && (
                          (pollData.questions || []).map((question, qIndex) => (
                            <div key={question.question_id} className="mb-4">
                              <div className="d-flex align-items-baseline">
                                <img
                                  src="/design/assets/poll2.svg"
                                  className="pollimg"
                                  alt="poll"
                                />
                                <div className="pollQ">
                                  <p>{question.question_text || question.question}</p>

                                  {!submitted ? (
                                    <div className="nuts">
                                      {(question.options || []).map((option, optionIndex) => (
                                        <div
                                          className={`form-check poll-checked ${answers[question.question_id] === optionIndex ? 'checked' : ''}`}
                                          key={option.option_id || optionIndex}
                                          onClick={() => handleChange(question.question_id, optionIndex)}
                                        >
                                          <input
                                            className="form-check-input"
                                            type="radio"
                                            name={`radioDefault${question.question_id}`}
                                            id={`radioDefault${question.question_id}${optionIndex+1}`}
                                            onChange={() => {}} // Handled by div onClick
                                            checked={answers[question.question_id] === optionIndex}
                                          />
                                          <label
                                            className="form-check-label"
                                            htmlFor={`radioDefault${question.question_id}${optionIndex+1}`}
                                          >
                                            {option.option_name || option}
                                          </label>
                                        </div>
                                      ))}
                                    </div>
                                  ) : (
                                    <div className="results">
                                      {(question.options || []).map((option, optionIndex) => {
                                        const count = votes[qIndex]?.counts?.[optionIndex] || 0;
                                        const total = votes[qIndex]?.total || 0;
                                        const percentage = total ? Math.round((count / total) * 100) : 0;
                                        return (
                                          <div key={option.option_id || optionIndex} className="mb-2">
                                            <div className="d-flex justify-content-between">
                                              <span>{option.option_name || option}</span>
                                              <small>{percentage}%</small>
                                            </div>
                                            <div className="progress-bar-1">
                                              <div
                                                className="progress-fill"
                                                style={{
                                                  width: `${percentage}%`,
                                                }}
                                              />
                                            </div>
                                            <div className="d-flex justify-content-between">
                                              <span></span>
                                              <small>{count} votes</small>
                                            </div>
                                          </div>
                                        );
                                      })}
                                      <div>
                                        <strong>
                                          Total votes: {votes[qIndex]?.total || 0}
                                        </strong>
                                      </div>
                                    </div>
                                  )}
                                </div>
                              </div>
                              <hr />
                            </div>
                          ))
                        )}

                        {!submitted && !loading && !error && pollData && (
                          <div className="submt">
                            <div className="it-nw-btn text-center mb-3">
                              <a
                                className="d-flex part justify-content-center align-items-center"
                                href="#"
                                onClick={handleSubmit}
                              >
                                Submit Poll
                              </a>
                            </div>
                          </div>
                        )}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* ✅ Popup */}
        {showPopup && (
          <div className="popup-overlay">
            <div className="popup-box">
              <div className="checkmark"></div>
              <h4>Thank you! Your response has been recorded.</h4>
              <button
                className="btn btn-sm btn-outline-secondary mt-3"
                onClick={handleClose}
              >
                Close
              </button>
            </div>
          </div>
        )}

        {/* Certificate Modal */}
        {showCertificateModal && (
          <div className="popup-overlay">
            <div className="popup-box">
              <div style={{ textAlign: 'center' }}>
                <h4 style={{ marginBottom: '20px', color: '#2e7d32' }}>Download Certificate</h4>
                <p style={{ marginBottom: '15px', fontSize: '14px' }}>
                  Your certificate for completing the poll "<strong>{pollData?.topic}</strong>" is ready to download.
                </p>
                <div style={{ marginTop: '25px', display: 'flex', gap: '10px', justifyContent: 'center', flexWrap: 'wrap' }}>
                  <button
                    className="btn btn-primary btn-sm"
                    onClick={handleDownloadCertificate}
                    disabled={isGeneratingCert}
                  >
                    {isGeneratingCert ? 'Generating...' : 'Download Certificate'}
                  </button>
                  <button
                    className="btn btn-outline-secondary btn-sm"
                    onClick={() => setShowCertificateModal(false)}
                    disabled={isGeneratingCert}
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
      </section>
    </>
  );
};

export default PollDetails;
