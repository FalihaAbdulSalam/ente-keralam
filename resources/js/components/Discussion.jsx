"use client";
import { useState, useEffect, useRef } from "react";
import { useNavigate, useParams } from "react-router-dom";
import {
  FaUser,
  FaCalendarAlt,
  FaSearch,
  FaFacebookF,
  FaInstagram,
  FaTwitter,FaComments,
  FaFilePdf
} from "react-icons/fa";
import { FaWhatsapp } from "react-icons/fa6";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import { discussionsAPI } from "../services/api";
import { useAuth } from "./App";

const Discussion = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();
  const [activeTab, setActiveTab] = useState("my");
  const [expanded, setExpanded] = useState(false);
  const [fileName, setFileName] = useState("Add Image/PDF");
  const [discussion, setDiscussion] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [showComments, setShowComments] = useState(false);
  const commentSectionRef = useRef(null);

  const [commentText, setCommentText] = useState("");
  const [selectedFile, setSelectedFile] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState({ type: "", message: "" });

  // Fetch discussion data from API
  useEffect(() => {
    async function fetchDiscussion() {
      try {
        setLoading(true);
        let response;
        if (id) {
          response = await discussionsAPI.getById(id);
        } else {
          response = await discussionsAPI.getAll();
        }

        if (response?.data?.status) {
          const data = response.data.data;
          if (id) {
            setDiscussion(data);
          } else if (Array.isArray(data) && data.length > 0) {
            setDiscussion(data[0]);
          }
        }
      } catch (err) {
        console.error("Failed to fetch discussion:", err);
        setError("Failed to load discussion data.");
      } finally {
        setLoading(false);
      }
    }
    fetchDiscussion();
  }, [id]);

  // Helper to get image URL
  const getImageUrl = (url) => {
    if (!url) return "/design/assets/dd.png";
    if (url.startsWith("http")) return url;
    return `https://entekeralam.kerala.gov.in/storage/${url}`;
  };

  // Helper to format date
  const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    } catch {
      return dateString;
    }
  };

  const handleParticipate = (e) => {
    if (e) e.preventDefault();
    if (isAuthenticated) {
    setShowComments(true);
    setTimeout(() => {
      if (commentSectionRef.current) {
        commentSectionRef.current.scrollIntoView({ behavior: "smooth" });
      }
    }, 100);
    } else {
      // Store the current discussion details page to redirect after login
      navigate('/login', { state: { from: window.location.pathname } });
    }
  };

  const handleFileChange = (e) => {
    if (e.target.files.length > 0) {
      const file = e.target.files[0];
      if (file.size > 2 * 1024 * 1024) {
        setSubmitStatus({ type: "error", message: "File size exceeds 2MB limit." });
        return;
      }
      setSelectedFile(file);
      setFileName(file.name);
    } else {
      setSelectedFile(null);
      setFileName("Add Image/PDF");
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!commentText.trim()) {
      setSubmitStatus({ type: "error", message: "Please enter a comment." });
      return;
    }

    try {
      setIsSubmitting(true);
      setSubmitStatus({ type: "", message: "" });

      const formData = new FormData();
      formData.append("discussion_id", discussion?.discussion_id || id || 1);
      formData.append("comment", commentText);
      formData.append("participant_id", user?.id);
      formData.append("approval_flag", 0);
      
      if (selectedFile) {
        formData.append("attachment", selectedFile);
      }

      const response = await discussionsAPI.submitResponse(formData);

      if (response.data.status) {
        setSubmitStatus({ type: "success", message: "Response submitted successfully and is pending approval." });
        setCommentText("");
        setSelectedFile(null);
        setFileName("Add Image/PDF");
        setExpanded(false);
      } else {
        setSubmitStatus({ type: "error", message: response.data.message || "Failed to submit response." });
      }
    } catch (err) {
      console.error("Submit error:", err);
      setSubmitStatus({
        type: "error",
        message: err.response?.data?.message || "An error occurred during submission."
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  const settings = {
    dots: true,
    arrows: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 800,
    slidesToShow: 2, // show 2 at once
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 768, // for mobile
        settings: { slidesToShow: 1 },
      },
    ],
    appendDots: (dots) => (
      <div
        style={{
          bottom: "-15px",
        }}
      >
        <ul style={{ margin: "0px" }}> {dots} </ul>
      </div>
    ),
    customPaging: (i) => (
      <div
        style={{
          width: "10px",
          height: "10px",
          borderRadius: "50%",
          background: "#d1d5db", // light gray for inactive
        }}
      ></div>
    ),
  };

  if (loading) {
    return (
      <div className="text-center py-5">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-5">
        <p className="text-danger">{error}</p>
      </div>
    );
  }

  return (
    <>
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="container">
          <div className="breadcurmb-title">
            <h2>Discussion</h2>
          </div>
          <div className="breadcurmb-item-list ul-li">
            <ul className="saasio-page-breadcurmb">
              <li>
                <a href="#">Home</a>
              </li>
              <li>
                <a href="#">Discussion</a>
              </li>
              <li>
                <a href="#">Discussion Details</a>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <section id="news-feed" className="news-feed-section">
        <div className="container-fluid">
          {/* <div className="blog-feed-content"> */}
          {/* Left Content */}
          <div className="col-md-8 col-12 mx-auto blog-feed-content">
            <div className="row">
              <div className="col-md-12">
                <div className="saasio-blog-details-content">
                  <div className="blog-details-text dia-headline wow fadeInTop">
                    <h2>{discussion?.discussion_title || "Discussion"}</h2>

                    {/* Post Meta */}
                    {/* <div className="saasio-post-meta dates">
                      <a href="#" className="date">
                        <svg
                          id="Layer_1"
                          className="svg-css mb-0"
                          width="13"
                          height="13"
                          enableBackground="new 0 0 512 512"
                          viewBox="0 0 512 512"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <g id="Layer_2_00000152227627889381600950000007803127182259535500_">
                            <g id="Layer_1_copy_2">
                              <g id="_108">
                                <path d="m334.8 314.4h-157.6c-97.7 0-177.2 79.5-177.2 177.2 0 11 9 20 20 20h472c11 0 20-9 20-20 0-97.7-79.5-177.2-177.2-177.2zm-293.3 157.2c9.7-66.2 66.9-117.2 135.8-117.2h157.5c68.9 0 126.1 51 135.8 117.2z" />
                                <path d="m256 279.4c76.9 0 139.5-62.6 139.5-139.5s-62.6-139.5-139.5-139.5-139.5 62.6-139.5 139.5 62.6 139.5 139.5 139.5zm0-239c55 0 99.5 44.5 99.5 99.5s-44.5 99.5-99.5 99.5-99.5-44.5-99.5-99.5c.1-54.9 44.6-99.4 99.5-99.5z" />
                              </g>
                            </g>
                          </g>
                        </svg>
                        <span>Fisheries Department</span>
                      </a>
                      <a href="#" className="date">
                        <svg
                          className="svg-css mb-0"
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
                        </svg>{" "}
                        Start : <span>{formatDate(discussion?.discussion_startDate)}</span>
                      </a>
                      <a href="#" className="date">
                        <svg
                          className="svg-css mb-0"
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
                        </svg>{" "}
                        End : <span>{formatDate(discussion?.discussion_endDate)}</span>
                      </a>
                    </div> */}
                  </div>

                  {/* Blog Image */}
                  <div
                    className="blog-details-img wow fadeInLeft"
                    data-wow-delay="200ms"
                    data-wow-duration="1500ms"
                  >
                    <img src={getImageUrl(discussion?.banner)} alt={discussion?.discussion_title || "Discussion"} />
                    {!isAuthenticated && <div className="overlayz">
                      {!showComments && (
                        <div
                          className="it-nw-btn text-center wow fadeInRight"
                          data-wow-delay="200ms"
                          data-wow-duration="1500ms"
                        >
                          <a
                            className="d-flex part justify-content-center align-items-center"
                            href="#"
                            onClick={handleParticipate}
                          >
                          Login to participate
                          </a>
                        </div>
                      )}
                    </div>}
                  </div>

                  <div className="blog-details-text dia-headline wow fadeInRight">
                    {discussion?.discussion_description && (
                      <article>
                        {discussion.discussion_description}
                      </article>
                    )}
                    {discussion?.discussion_content && (
                      <article
                        dangerouslySetInnerHTML={{
                          __html: discussion.discussion_content,
                        }}
                      />
                    )}
                    {/* PDF Buttons */}
                    <div className="d-flex flex-wrap gap-3 mt-4">
                      {/* {discussion?.malayalam_pdf && ( */}
                        <a
                          className="d-flex align-items-center justify-content-center mr-3"
                          href={getImageUrl(discussion.attachment_mal)}
                          target="_blank"
                          rel="noopener noreferrer"
                          style={{ 
                            padding: '8px 16px', 
                            borderRadius: '4px', 
                            fontSize: '14px', 
                            border: '1px solid #ddd', 
                            color: '#555',
                            textDecoration: 'none',
                            background: '#fff'
                          }}
                        >
                          <FaFilePdf className="mr-2 text-danger" /> Malayalam PDF
                        </a>
                      {/* )} */}
                      {/* {discussion?.english_pdf && ( */}
                        <a
                          className="d-flex align-items-center justify-content-center"
                          href={getImageUrl(discussion.attachment_en)}
                          target="_blank"
                          rel="noopener noreferrer"
                          style={{ 
                            padding: '8px 16px', 
                            borderRadius: '4px', 
                            fontSize: '14px', 
                            border: '1px solid #ddd', 
                            color: '#555',
                            textDecoration: 'none',
                            background: '#fff'
                          }}
                        >
                          <FaFilePdf className="mr-2 text-danger" /> English PDF
                        </a>
                      {/* )} */}
                    </div>
                  </div>

                  {/* Hide / Share */}
                  <div className="mt-4">
                    {!isAuthenticated && (
                      <a
                        href="#"
                        className="hidex d-inline-flex align-items-center"
                        onClick={handleParticipate}
                      >
                        <FaComments className="mr-2" />
                        Post Your Comments
                      </a>
                    )}                    <div className="blog-feed-share float-right">
                      <small>Share:</small>
                      <a href="#">
                        <img src="/design/assets/social/facebook.svg" width="22" alt="" />
                      </a>
                      <a href="#">
                        <img src="/design/assets/social/insta.svg" width="22" alt="" />
                      </a>
                      <a href="#">
                        <img src="/design/assets/social/whatsapp.svg" width="22" alt="" />
                      </a>
                      <a href="#">
                        <img src="/design/assets/social/twitter.svg" width="22" alt="" />
                      </a>
                    </div>
                  </div>

                  <hr />

                  {/* Comments */}
                  {(showComments || isAuthenticated) && (
                    <div
                      className="it-nw-about-tab-wrapper wow fadeInUp"
                      data-wow-delay="200ms"
                      data-wow-duration="1500ms"
                      ref={commentSectionRef}
                    >
                      {/* Tabs */}
                      <div className="it-nw-about-tab-btn">
                        <ul id="tabs" className="nav text-capitalize nav-tabs">
                          <li className="nav-item">
                            <a
                              className="nav-link text-capitalize active show"
                              onClick={() => setActiveTab("my")}
                            >
                              Comments
                            </a>
                          </li>
                        </ul>
                      </div>

                      {/* Comment Input */}
                      <div className="my-text1">
                        <img src={user?.avatar || "/design/assets/prof.jpg"} alt="profile" style={{ borderRadius: '50%' }} />
                        <div className="w-100">
                          {!isAuthenticated ? (
                            <div className="w-100">
                              <a
                                className="d-flex part justify-content-center align-items-center w-100"
                                href="#"
                                onClick={handleParticipate}
                                style={{
                                  height: '100px',
                                  background: '#f8f9fa',
                                  border: '1px solid #ddd',
                                  color: '#6c757d',
                                  borderRadius: '10px',
                                  fontSize: '1.1rem',
                                  textDecoration: 'none'
                                }}
                              >
                                Login to participate
                              </a>
                            </div>
                          ) : !expanded ? (
                            <input
                              type="text"
                              className="form-control form-control-lg"
                              id="input-field"
                              placeholder="Write your Opinion"
                              onFocus={() => setExpanded(true)}
                            />
                          ) : (
                            <div
                              className="text-era"
                              id="text-era"
                              style={{ display: "block" }}
                            >
                              <textarea
                                className="w-100 form-control"
                                placeholder="Share Your Views..."
                                rows="4"
                                spellCheck="false"
                                value={commentText}
                                onChange={(e) => setCommentText(e.target.value)}
                                disabled={isSubmitting}
                              ></textarea>

                              {submitStatus.message && (
                                <div className={`mt-2 alert ${submitStatus.type === "success" ? "alert-success" : "alert-danger"}`}>
                                  {submitStatus.message}
                                </div>
                              )}

                              {/* File Upload */}
                              <input
                                type="file"
                                id="actual-btn"
                                hidden
                                onChange={handleFileChange}
                                disabled={isSubmitting}
                                accept=".jpg,.jpeg,.png,.pdf"
                              />
                              <div className="d-flex align-items-center justify-content-between mt-2">
                                <div>
                                  <label htmlFor="actual-btn" className="file-label mb-0" style={{ cursor: 'pointer' }}>
                                    Choose File
                                  </label>
                                  <span className="ms-1" id="file-chosen">
                                    {fileName}
                                  </span>
                                </div>

                                {/* Submit */}
                                <div className="it-nw-btn text-center">
                                  <a
                                    className="d-flex part justify-content-center align-items-center border-0"
                                    onClick={handleSubmit}
                                    disabled={isSubmitting}
                                    style={{ color: 'white', padding: '10px 25px', borderRadius: '5px' }}
                                  >
                                    {isSubmitting ? "Submitting..." : "Submit"}
                                  </a>
                                </div>
                              </div>
                            </div>
                          )}
                        </div>
                      </div>

                      {/* Comments List */}
                      <div className="discuss ul-li-block pera-content">
                        <div id="tabsContent" className="tab-content">
                          {activeTab === "all" && (
                            <div id="all-c" className="tab-pane fade active show">
                              <ul>
                                <li className="parent">
                                  <div className="pro-sec">
                                    <img src="/design/assets/comment/team-2.jpg" alt="" />
                                    <div>
                                      <h4>Angoor Ravuthar</h4>
                                      <span>12 hours 29 minutes ago</span>
                                    </div>
                                  </div>
                                  <p>
                                    Lorem ipsum dolor sit amet consectetur
                                    adipisicing elit. Cum dolores dolore sed
                                    expedita, recusandae repellendus aperiam
                                    maxime aliquid voluptas dicta necessitatibus
                                    quidem doloribus excepturi molestias illo quas
                                    aspernatur iste repellat. Lorem ipsum dolor
                                    sit amet consectetur adipisicing elit. Cum
                                    dolores dolore sed expedita, recusandae
                                    repellendus aperiam maxime aliquid voluptas
                                    dicta necessitatibus quidem doloribus
                                    excepturi molestias illo quas aspernatur iste
                                    repellat.
                                  </p>
                                  <div className="action-sec"></div>
                                </li>
                              </ul>
                            </div>
                          )}
                          {activeTab === "my" && (
                            <div id="my-c" className="tab-pane fade active show">
                              {/* rrrrrrr */}
                            </div>
                          )}
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
          {/* </div> */}
        </div>
      </section>
    </>
  );
};

export default Discussion;
