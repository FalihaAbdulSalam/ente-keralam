import React, { useState, useEffect, useRef } from "react";
import { useParams, Navigate } from "react-router-dom";
import { competitionAPI, activityAPI } from "../../services/api";
import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import "./CompetitionDetails.css";
import NotFound from "../NotFound";
import { FaDownload } from "react-icons/fa";

const CompetitionDetails = () => {
    const { id } = useParams();
    const [contest, setContest] = useState(null);
    const [loading, setLoading] = useState(true);
    const [notFound, setNotFound] = useState(false);
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [isParticipating, setIsParticipating] = useState(false);

    const [file, setFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState("");
    const [showModal, setShowModal] = useState(false);
    const [selectedVideo, setSelectedVideo] = useState(null);
    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [submitting, setSubmitting] = useState(false);
    const [submitMessage, setSubmitMessage] = useState("");
    const [submitError, setSubmitError] = useState("");

    // null = not fetched yet, false = fetched and confirmed no submission, object = submission
    const [mySubmission, setMySubmission] = useState(null);
    const [loadingSubmission, setLoadingSubmission] = useState(false);
    const [submissionError, setSubmissionError] = useState("");
    const [isEditingSubmission, setIsEditingSubmission] = useState(false);
    const [updatingSubmission, setUpdatingSubmission] = useState(false);
    const [updateMessage, setUpdateMessage] = useState("");
    const [updateError, setUpdateError] = useState("");

    const getStatusLabel = (status) => {
        const s = Number(status);
        if (Number.isNaN(s)) return "Unknown";
        // Common mapping: 0=pending, 1=approved. Adjust easily if backend changes.
        switch (s) {
            case 0:
                return "Pending";
            case 1:
                return "Approved";
            case 2:
                return "Rejected";
            default:
                return `Status ${s}`;
        }
    };

    const getVideoUrlFromSubmission = (sub) =>
        sub?.video_light_url ||
        sub?.video_heavy_url ||
        sub?.video_url ||
        sub?.video_file ||
        "";

    const getPdfUrlFromSubmission = (sub) => sub?.pdf_file || "";

    const uploadSectionRef = useRef(null);

    // Prevent repeated auto-detect requests for the same user+contest.
    // This avoids accidental loops caused by rerenders and state toggles.
    const autoDetectKeyRef = useRef(null);

    const scrollToUploadSection = (options = {}) => {
        const {
            offset = 90,
            behavior = "smooth",
            maxTries = 20,
            intervalMs = 50,
        } = options;

        let tries = 0;
        const attempt = () => {
            const el = uploadSectionRef.current;
            if (!el) {
                tries += 1;
                if (tries <= maxTries) setTimeout(attempt, intervalMs);
                return;
            }

            const { top } = el.getBoundingClientRect();
            const y = window.pageYOffset + top - offset;
            window.scrollTo({ top: Math.max(0, y), behavior });
        };

        // Let React paint the upload section first, then scroll.
        requestAnimationFrame(() => setTimeout(attempt, 0));
    };

    useEffect(() => {
        const fetchContest = async () => {
            if (!id) return setLoading(false);
            try {
                setLoading(true);
                setNotFound(false);
                // Use getBySlug since URL uses slug parameter
                const response = await competitionAPI.getBySlug(id);
                const payload = response.data?.data ?? response.data ?? null;
                
                if (!payload) {
                    setNotFound(true);
                    setContest(null);
                } else {
                    setContest(payload);
                }
            } catch (error) {
                console.error("Error fetching contest:", error);
                // Check if it's a 404 error
                if (error.response?.status === 404) {
                    setNotFound(true);
                }
                setContest(null);
            } finally {
                setLoading(false);
            }
        };

        fetchContest();

        // Check if user is logged in
        const token = localStorage.getItem('token');
        const user = localStorage.getItem('user');
        if (token && user) {
            setIsLoggedIn(true);
        }
    }, [id]);

    // If the user is already logged in and has a submission for this contest,
    // auto-enable participating so they see their submission + upload form without
    // needing to click the Participate button again.
    useEffect(() => {
        if (!isLoggedIn) return;
        if (!contest) return;
        if (isParticipating) return;

        // If we're already fetching the submission due to some other trigger,
        // don't start another request.
        if (loadingSubmission) return;

        const contestId = getNumericContestId();
        const applicantId = getApplicantId();
        if (!contestId || !applicantId) return;

    const key = `${contestId}:${applicantId}`;
    if (autoDetectKeyRef.current === key) return;
    autoDetectKeyRef.current = key;

        let cancelled = false;

        (async () => {
            try {
                const payload = await fetchMySubmission();
                if (cancelled) return;
                if (payload) {
                    setIsParticipating(true);
                }
            } catch {
                // Keep the normal flow; user can click Participate.
            }
        })();

        return () => {
            cancelled = true;
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [isLoggedIn, contest, isParticipating, loadingSubmission]);

    // Fallback: in case participate state is set elsewhere, still auto-scroll.
    useEffect(() => {
        if (!isLoggedIn || !isParticipating) return;
        scrollToUploadSection();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [isLoggedIn, isParticipating]);

    const handleFileChange = (e) => {
        const uploadedFile = e.target.files[0];
        if (uploadedFile) {
            const pdfContest = isPdfContest();
            const isPdfFile = uploadedFile.type
                ?.toLowerCase()
                .includes("pdf");

            if (pdfContest && !isPdfFile) {
                setSubmitError("Please upload a PDF file for this contest");
                setUpdateError("");
                e.target.value = "";
                return;
            }
            setFile(uploadedFile);
            setPreviewUrl(URL.createObjectURL(uploadedFile));
            setSubmitError("");
            setUpdateError("");
        }
    };

    const handleDelete = () => {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
        setFile(null);
        setPreviewUrl("");
    };

    const handleLogin = () => {
        setIsLoggedIn(true);
    };

    const handleParticipate = () => {
        setIsParticipating(true);
    };

    const getApplicantId = () => {
        const userStr = localStorage.getItem("user");
        if (!userStr) return null;
        try {
            const user = JSON.parse(userStr);
            return user.id || user.user_id || user.applicant_id || null;
        } catch {
            return null;
        }
    };

    const getNumericContestId = () => {
        const raw = contest?.id ?? contest?.contest_id ?? contest?.contestId;
        const n = Number(raw);
        return !n || Number.isNaN(n) ? null : n;
    };

    const isPdfContest = () => {
        const cid = getNumericContestId();
        const typeText = (
            contest?.type ||
            contest?.contest_type ||
            contest?.category ||
            contest?.contest_name ||
            ""
        )
            .toString()
            .toLowerCase();

        if (typeText.includes("essay") || typeText.includes("poem")) {
            return true;
        }

        // Fallback to known ids (4: essay, 5: poem)
        return cid === 4 || cid === 5;
    };

    const fetchMySubmission = async () => {
        const contestId = getNumericContestId();
        const applicantId = getApplicantId();
        if (!contestId || !applicantId) return null;

        setLoadingSubmission(true);
        setSubmissionError("");
        try {
            if (isPdfContest()) {
                const res = await competitionAPI.getContestPdfSubmission({
                    contest_id: contestId,
                    applicant_id: Number(applicantId),
                });

                const payload = res.data?.data ?? null;
                const success = res.data?.success;

                if (!payload || success === false) {
                    setMySubmission(false);
                    return null;
                }

                setMySubmission(payload);
                return payload;
            } else {
                const res = await competitionAPI.getReel({
                    contest_id: contestId,
                    applicant_id: Number(applicantId),
                });
                
                // Handle both success:true with data:null and actual data
                const payload = res.data?.data ?? null;
                
                // If payload is null or empty, treat as "no submission" (not an error)
                if (!payload) {
                    setMySubmission(false);
                    return null;
                }
                
                setMySubmission(payload);
                return payload;
            }
        } catch (err) {
            // 404 or no submission found - this is normal for first-time users
            if (err?.response?.status === 404) {
                setMySubmission(false);
                return null;
            }
            // For other errors, still set submission to false but don't show error
            // unless it's a real server error (5xx)
            setMySubmission(false);
            if (err?.response?.status >= 500) {
                setSubmissionError(
                    err?.response?.data?.message ||
                        "Unable to fetch your submission. Please try again."
                );
            }
            // Don't throw - just return null so the flow continues
            return null;
        } finally {
            setLoadingSubmission(false);
        }
    };

    // If we have a submission, ensure the section becomes visible without user clicking Participate.
    useEffect(() => {
        if (!isLoggedIn) return;
        if (!mySubmission || mySubmission === false) return;
        if (isParticipating) return;
        setIsParticipating(true);
    }, [isLoggedIn, mySubmission, isParticipating]);

    // Note: We intentionally do NOT auto-fetch the submission every time
    // `isParticipating` flips to true.
    //
    // - On page load, the auto-detect effect calls `fetchMySubmission()` once
    //   and enables participating if a submission exists.
    // - After upload/update, we explicitly refresh via `fetchMySubmission()`.
    //
    // This avoids duplicate API calls on initial load and on Participate click.

    const fileToBase64 = (inputFile) => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(inputFile);
            reader.onload = () => {
                const base64String = reader.result.split(",")[1];
                resolve(base64String);
            };
            reader.onerror = (error) => reject(error);
        });
    };

    const fileToDataUrl = (inputFile) => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(inputFile);
            reader.onload = () => resolve(reader.result);
            reader.onerror = (error) => reject(error);
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        // Validate required fields
        if (!file) {
            setSubmitError("Please select a file to upload");
            return;
        }
        if (!title.trim()) {
            setSubmitError("Please enter a title");
            return;
        }

        try {
            const numericContestId = getNumericContestId();
            if (!numericContestId) {
                setSubmitError(
                    "Contest ID not found. Please refresh the page and try again."
                );
                return;
            }

            const applicantId = getApplicantId();
            if (!applicantId) {
                setSubmitError("Please login to submit");
                setIsLoggedIn(false);
                return;
            }

            setSubmitting(true);
            setSubmitError("");
            setSubmitMessage("");

            let uploadResponse;
            const pdfContest = isPdfContest();
            const isPdfFile = file && file.type?.toLowerCase().includes("pdf");

            if (pdfContest && !isPdfFile) {
                setSubmitError("Please upload a PDF file for this contest");
                return;
            }

            if (pdfContest) {
                const form = new FormData();
                form.append("title", title.trim());
                form.append("description", description.trim());
                form.append("contest_id", String(numericContestId));
                form.append("applicant_id", String(parseInt(applicantId)));
                form.append("pdf_file", file);

                uploadResponse = await competitionAPI.uploadContestPdf(form);
            } else if (isVideo) {
                // IMPORTANT: Large files should NOT be converted to base64/DataURL.
                // That duplicates memory and can crash the tab for 200MB+.
                const form = new FormData();
                form.append('contest_id', String(numericContestId));
                form.append('applicant_id', String(parseInt(applicantId)));
                form.append('video_title', title.trim());
                form.append('remarks', description.trim() || '');
                form.append('video', file);

                uploadResponse = await competitionAPI.uploadReelMultipart(form);
            } else {
                const imageBase64 = await fileToBase64(file);
                const submissionData = {
                    contest_id: numericContestId,
                    applicant_id: parseInt(applicantId),
                    upload_title: title.trim(),
                    imageBase64: imageBase64,
                    imageName: file.name,
                    imageType: file.type,
                    remarks: description.trim() || "",
                };
                uploadResponse = await competitionAPI.uploadPhoto(submissionData);
            }

            // Record activity and award points
            let pointsEarned = 0;
            if (contest?.score && contest?.contest_id) {
                try {
                    const activityResponse = await activityAPI.submitCompetition(contest.contest_id, {
                        contest_name: contest.contest_name || contest.title || 'Competition',
                        contest_type: contest.type || 'Competition',
                        score: contest.score,
                    });
                    pointsEarned = activityResponse.data?.data?.points_earned || contest.score;
                    
                    // Show success toast with points awarded
                    toast.success(`🎉 Congratulations! You've been awarded ${pointsEarned} points!`, {
                        position: "top-right",
                        autoClose: 5000,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                    });
                } catch (activityError) {
                    console.error('Failed to record activity:', activityError);
                    // Show warning toast if points recording fails
                    toast.warning('Submission successful, but points could not be recorded. Please contact support.', {
                        position: "top-right",
                        autoClose: 5000,
                    });
                }
            }

            const scoreMsg = pointsEarned > 0 
                ? ` You have been awarded ${pointsEarned} points!` 
                : (contest?.score ? ` You have been awarded ${contest.score} points!` : '');
            const uploadType = pdfContest ? 'PDF' : isVideo ? 'Video' : 'Photo';
            setSubmitMessage(`${uploadType} uploaded successfully!${scoreMsg}`);
            
            // Also show upload success toast
            toast.success(`${uploadType} uploaded successfully!`, {
                position: "top-right",
                autoClose: 3000,
            });

            // Refresh panel after successful upload
            fetchMySubmission();

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }
            setFile(null);
            setPreviewUrl("");
            setTitle("");
            setDescription("");
            
            // Clear success message after 5 seconds
            setTimeout(() => {
                setSubmitMessage("");
            }, 5000);

        } catch (error) {
            console.error("Error submitting entry:", error);
            const errorMessage = error.response?.data?.message || 
                               error.response?.data?.error || 
                               "Failed to upload. Please try again.";
            setSubmitError(errorMessage);
            
            // Show error toast
            toast.error(errorMessage, {
                position: "top-right",
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
            });
        } finally {
            setSubmitting(false);
        }
    };

    const handleUpdateSubmission = async (e) => {
        e.preventDefault();

        setUpdateMessage("");
        setUpdateError("");

        const contestId = getNumericContestId();
        const applicantId = getApplicantId();
        if (!contestId || !applicantId) {
            setUpdateError("Missing contest/applicant information.");
            return;
        }
        if (!title.trim()) {
            setUpdateError("Please enter a title");
            return;
        }

        try {
            setUpdatingSubmission(true);

            const pdfContest = isPdfContest();
            const isPdfFile = file && file.type?.toLowerCase().includes("pdf");

            if (pdfContest && !isPdfFile) {
                setUpdateError("Please upload a PDF file for this contest");
                return;
            }

            if (pdfContest) {
                const form = new FormData();
                form.append("title", title.trim());
                form.append("description", description.trim() || "");
                form.append("contest_id", String(contestId));
                form.append("applicant_id", String(Number(applicantId)));
                form.append("pdf_file", file);

                await competitionAPI.updateContestPdf(form);
            } else {
                // Update via multipart; include video only if user selected a new one.
                const form = new FormData();
                form.append('contest_id', String(contestId));
                form.append('applicant_id', String(Number(applicantId)));
                form.append('video_title', title.trim());
                form.append('remarks', description.trim() || '');
                if (file && isVideo) {
                    form.append('video', file);
                }

                await competitionAPI.updateReelMultipart(form);
            }
            setUpdateMessage("Submission updated successfully!");
            setIsEditingSubmission(false);
            await fetchMySubmission();
            
            // Show success toast for update
            toast.success("Submission updated successfully!", {
                position: "top-right",
                autoClose: 3000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
            });
        } catch (err) {
            const status = err?.response?.status;
            const apiMessage = err?.response?.data?.message;
            const apiErrors = err?.response?.data?.errors;

            let errorMsg = apiMessage || "Failed to update submission. Please try again.";

            // Show first validation error if present
            if (apiErrors && typeof apiErrors === 'object') {
                const firstKey = Object.keys(apiErrors)[0];
                const firstVal = apiErrors[firstKey];
                const firstText = Array.isArray(firstVal) ? firstVal[0] : String(firstVal || '');
                if (firstKey && firstText) {
                    errorMsg = `${firstKey}: ${firstText}`;
                }
            }

            // Common proxy/server limits
            if (status === 413) {
                errorMsg = 'Video file too large for the server (413). Please upload a smaller file or increase server upload limits.';
            }
            // Network-level failures (timeout/disconnect)
            if (!err?.response && err?.message) {
                errorMsg = err.message;
            }
            setUpdateError(errorMsg);
            
            // Show error toast for update
            toast.error(errorMsg, {
                position: "top-right",
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
            });
        } finally {
            setUpdatingSubmission(false);
        }
    };

    const isVideo = file && file.type.startsWith("video");
    const isPdf = file && file.type?.toLowerCase().includes("pdf");
    const fileAccept = isPdfContest() ? "application/pdf" : "image/*,video/*";

    // Show 404 page if competition not found
    if (!loading && notFound) {
        return <NotFound />;
    }

    return (
        <div className="competition-container">
            <ToastContainer />
            {/* <img
                src="/design/assets/fp/frame.svg"
                alt="Left frame"
                className="banner-svg left"
            /> */}
            <div className="image-wrapper">
    <img
        src="/design/assets/fp/Frame.svg"
        alt="Left frame"
        className="banner-svg"
    />

    <div className="image-text">
         {contest?.title}
    </div>
</div>


            {/* Banner Section */}

            {/* Content Section */}
            <div className="content container">
                <div className="row justify-content-center">
                    <div className="it-nw-side-bg-top text-center position-absolute">
                        <img src="/design/assets/background/sides.svg" alt="" />
                    </div>
                    {/* Competition Info Box */}
                    <div className="col-md-8 banner banner-pd mt-4 mb-3">
                        <h3>
                            {loading
                                ? "Loading..."
                                : contest?.contest_name || "Competition"}
                        </h3>
                    </div>
                    <div className="col-md-8 competition-card">
                        <div className="card-content">
                            <img
                                src={
                                    contest?.banner ||
                                    contest?.poster
                                }
                                alt="Poll Banner"
                                className="poll-image"
                            />
                            {/* <div className="poll-details">
                                <p>Start Date: 22.12.2022</p>
                                <p>End Date: 28.12.2022</p>
                                <button className="login-btn">
                                    Login to participate
                                </button>
                            </div> */}
                            <div className="poll-details mb-4">
                                <div className="detail-row">
                                    <span className="label">
                                        Start Date{" "}
                                        <span className="ml-1">:</span>
                                    </span>
                                    <span className="value date-large">
                                        {contest?.start_date || "—"}
                                    </span>
                                </div>
                                <div className="detail-row">
                                    <span className="label">
                                        End Date{" "}
                                        <span className="ml-2"> :</span>
                                    </span>
                                    <span className="value date-large">
                                        {contest?.end_date || "—"}
                                    </span>
                                </div>

                                {(!mySubmission || mySubmission === false) && (
                                    <button
                                        className="login-btn mt-3"
                                        disabled={loadingSubmission}
                                        onClick={() => {
                                            if (!isLoggedIn) {
                                                const redirectTo = `${window.location.pathname}${window.location.search || ""}${window.location.hash || ""}`;
                                                return (window.location.href =
                                                    `/login?redirect=${encodeURIComponent(redirectTo)}`);
                                            }

                                            setSubmitError("");
                                            setIsParticipating(true);
                                            scrollToUploadSection();
                                        }}
                                    >
                                                                                {loadingSubmission
                                                                                        ? "Checking..."
                                                                                        : isLoggedIn
                                                                                            ? "Participate"
                                                                                            : "Login to participate"}
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                    <div className="col-md-8  mt-4 pl-0 pr-0">
                        {contest?.description && (
                            <div className="competition-description mt-3">
                                {/* <h4 className="description-title">
                                    {contest?.title}
                                </h4> */}
                                <p>{contest.description}</p>
                            </div>
                            
                        )}
                        {contest?.instructions && (
                            <div className="mt-4 competition-instructions-card px-3 pt-3 pb-4 rounded-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 shadow-sm" style={{background: "#f4f7ff", border: "1px solid #d9e3ff"}}>
                                <div className="flex-grow-1">
                                    <div className="text-uppercase text-danger small fw-semibold mb-1">Instructions</div>
                                    <div className="h6 mb-0 fw-bold text-dark" style={{letterSpacing: "0.2px"}}>{contest.instructions_text ? contest.instructions_text : "View/Download the instructions before participating"}</div>
                                </div>
                                <a
                                    href={contest.instructions}
                                    className="btn download-pill d-inline-flex align-items-center gap-2 px-4 py-3 mt-4"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style={{background: "#003399", borderColor: "#003399", fontWeight: 700, borderRadius: "999px", color: "#fff", boxShadow: "0 10px 25px rgba(0,51,153,0.25)"}}
                                >
                                    <FaDownload size={18} /> View/Download PDF
                                </a>
                            </div>
                        )}
                        {contest?.subject && contest?.sub_file && (
                            <div className="mt-4 competition-subject-card px-3 pt-3 pb-4 rounded-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 shadow-sm" style={{background: "#f4f7ff", border: "1px solid #d9e3ff"}}>
                                <div className="flex-grow-1">
                                    <div className="text-uppercase text-danger small fw-semibold mb-1">Please read before applying</div>
                                    <div className="h6 mb-0 fw-bold text-dark" style={{letterSpacing: "0.2px"}}>{contest.subject}</div>
                                </div>
                                <a
                                    href={contest.sub_file}
                                    className="btn download-pill d-inline-flex align-items-center gap-2 px-4 py-3 mt-4"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style={{background: "#003399", borderColor: "#003399", fontWeight: 700, borderRadius: "999px", color: "#fff", boxShadow: "0 10px 25px rgba(0,51,153,0.25)"}}
                                >
                                    <FaDownload size={18} /> View/Download PDF
                                </a>
                            </div>
                        )}
                    </div>

                    <div className="col-md-8 upload-section text-center mt-4" ref={uploadSectionRef}>
                        <div className="upload-wrapper">
                            {isParticipating && (
                                <>
                                    {(loadingSubmission || submissionError) && (
                                        <div className="mb-3">
                                            {loadingSubmission && (
                                                <div className="alert alert-info" role="alert">
                                                    Loading your submission...
                                                </div>
                                            )}
                                            {submissionError && (
                                                <div className="alert alert-warning" role="alert">
                                                    {submissionError}
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    {mySubmission && !isEditingSubmission && (
                                        <div className="card p-3 text-start mb-3">
                                            <div className="d-flex justify-content-between align-items-start">
                                                <div className="mx-auto">
                                                    <h5 className="mb-1">Your submission</h5>
                                                    <div className="text-muted" style={{ fontSize: 13 }}>
                                                        View your uploaded entry and edit it if needed.
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="mt-3">
                                                {!!getVideoUrlFromSubmission(mySubmission) && (
                                                    <video
                                                        controls
                                                        src={getVideoUrlFromSubmission(mySubmission)}
                                                        style={{ width: "100%", borderRadius: 12 }}
                                                    />
                                                )}
                                                {isPdfContest() &&
                                                    getPdfUrlFromSubmission(mySubmission) && (
                                                        <div className="video-preview-box mt-2">
                                                            <div className="video-info">
                                                                <span className="video-icon">📄</span>
                                                                <span className="video-name">Poem / Essay PDF</span>
                                                                <a
                                                                    href={getPdfUrlFromSubmission(mySubmission)}
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    className="login-btn"
                                                                    style={{ padding: "6px 12px", marginLeft: 10 }}
                                                                >
                                                                    View PDF
                                                                </a>
                                                            </div>
                                                        </div>
                                                    )}
                                            </div>

                                            <div className="mt-3">
                                                <div className="fw-semibold">Title</div>
                                                <div className="text-muted">
                                                    {mySubmission?.title ||
                                                        mySubmission?.video_title ||
                                                        mySubmission?.upload_title ||
                                                        "—"}
                                                </div>
                                            </div>

                                            <div className="mt-2">
                                                <div className="fw-semibold">Remarks</div>
                                                <div className="text-muted">
                                                    {mySubmission?.description ||
                                                        mySubmission?.remarks ||
                                                        "—"}
                                                </div>
                                            </div>

                                            <div className="mt-3 text-center">
                                                <button
                                                    type="button"
                                                    className="login-btn"
                                                    onClick={() => {
                                                        setUpdateMessage("");
                                                        setUpdateError("");
                                                        setIsEditingSubmission(true);
                                                        setTitle(
                                                            mySubmission?.title ||
                                                            mySubmission?.video_title ||
                                                                mySubmission?.upload_title ||
                                                                ""
                                                        );
                                                        setDescription(
                                                            mySubmission?.description ||
                                                                mySubmission?.remarks ||
                                                                ""
                                                        );
                                                    }}
                                                >
                                                    Edit submission
                                                </button>
                                            </div>
                                        </div>
                                    )}

                                    {mySubmission && isEditingSubmission && (
                                        <form onSubmit={handleUpdateSubmission}>
                                            <div className="text-end mb-2">
                                                <button
                                                    type="button"
                                                    className="btn btn-link btn-sm text-decoration-none"
                                                    onClick={() => {
                                                        setIsEditingSubmission(false);
                                                        setUpdateError("");
                                                        setUpdateMessage("");
                                                        setFile(null);
                                                        setPreviewUrl("");
                                                    }}
                                                >
                                                    Cancel
                                                </button>
                                            </div>

                                            <label className="upload-box">
                                                <input
                                                    type="file"
                                                    accept={fileAccept}
                                                    onChange={handleFileChange}
                                                    disabled={updatingSubmission}
                                                    hidden
                                                />
                                                <div className="upload-content">
                                                    <img
                                                        className="upload-icon"
                                                        src="/design/assets/background/Vector.svg"
                                                    />
                                                    <div>Browse file to upload</div>
                                                </div>
                                            </label>

                                            {file && !isVideo && !isPdf && (
                                                <div className="image-preview">
                                                    <img src={previewUrl} alt="Preview" />
                                                    <button
                                                        type="button"
                                                        className="delete-btn"
                                                        onClick={handleDelete}
                                                    >
                                                        ✖
                                                    </button>
                                                </div>
                                            )}

                                            {file && isVideo && (
                                                <div className="video-preview-box">
                                                    <div className="video-info">
                                                        <span className="video-icon">🎥</span>
                                                        <span className="video-name">{file.name}</span>
                                                        <button
                                                            type="button"
                                                            className="play-btn"
                                                            onClick={() => setShowModal(true)}
                                                        >
                                                            ▶
                                                        </button>
                                                        <button
                                                            type="button"
                                                            className="delete-btn"
                                                            onClick={handleDelete}
                                                        >
                                                            ✖
                                                        </button>
                                                    </div>
                                                </div>
                                            )}

                                            {file && isPdf && (
                                                <div className="video-preview-box">
                                                    <div className="video-info">
                                                        <span className="video-icon">📄</span>
                                                        <span className="video-name">{file.name}</span>
                                                        <a
                                                            href={previewUrl}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="play-btn"
                                                        >
                                                            View
                                                        </a>
                                                        <button
                                                            type="button"
                                                            className="delete-btn"
                                                            onClick={handleDelete}
                                                        >
                                                            ✖
                                                        </button>
                                                    </div>
                                                </div>
                                            )}

                                            <p className="guidelines">
                                                <span className="red">*</span>{" "}
                                                {contest?.add_info?.trim()
                                                    ? contest.add_info
                                                    : "No additional information available."}
                                            </p>

                                            <div className="form-group">
                                                <label htmlFor="edit-title" className="title-heading">
                                                    Title
                                                </label>
                                                <input
                                                    type="text"
                                                    id="edit-title"
                                                    className="form-control title-input"
                                                    value={title}
                                                    onChange={(e) => {
                                                        setTitle(e.target.value);
                                                        setUpdateError("");
                                                    }}
                                                    disabled={updatingSubmission}
                                                    required
                                                />
                                            </div>

                                            <div className="form-group">
                                                <label htmlFor="edit-description" className="description-heading">
                                                    Description
                                                </label>
                                                <textarea
                                                    id="edit-description"
                                                    className="form-control description-input"
                                                    value={description}
                                                    onChange={(e) => {
                                                        setDescription(e.target.value);
                                                        setUpdateError("");
                                                    }}
                                                    disabled={updatingSubmission}
                                                    rows="4"
                                                />
                                            </div>

                                            {updateError && (
                                                <div className="alert alert-danger mt-3" role="alert">
                                                    {updateError}
                                                </div>
                                            )}

                                            {updateMessage && (
                                                <div className="alert alert-success mt-3" role="alert">
                                                    {updateMessage}
                                                </div>
                                            )}

                                            <button
                                                type="submit"
                                                className="submit-btn"
                                                disabled={updatingSubmission}
                                            >
                                                {updatingSubmission ? "Updating..." : "Update"}
                                            </button>
                                        </form>
                                    )}

                                    {!isEditingSubmission && !mySubmission && (
                                        <form onSubmit={handleSubmit}>
                                            <label className="upload-box">
                                                <input
                                                    type="file"
                                                    accept={fileAccept}
                                                    onChange={handleFileChange}
                                                    disabled={submitting}
                                                    hidden
                                                />
                                                <div className="upload-content">
                                                    <img
                                                        className="upload-icon"
                                                        src="/design/assets/background/Vector.svg"
                                                    />
                                                    <div>Browse file to upload</div>
                                                </div>
                                            </label>

                                            {file && !isVideo && !isPdf && (
                                                <div className="image-preview">
                                                    <img src={previewUrl} alt="Preview" />
                                                    <button
                                                        type="button"
                                                        className="delete-btn"
                                                        onClick={handleDelete}
                                                    >
                                                        ✖
                                                    </button>
                                                </div>
                                            )}

                                            {file && isVideo && (
                                                <div className="video-preview-box">
                                                    <div className="video-info">
                                                        <span className="video-icon">🎥</span>
                                                        <span className="video-name">{file.name}</span>
                                                        <button
                                                            type="button"
                                                            className="play-btn"
                                                            onClick={() => setShowModal(true)}
                                                        >
                                                            ▶
                                                        </button>
                                                        <button
                                                            type="button"
                                                            className="delete-btn"
                                                            onClick={handleDelete}
                                                        >
                                                            ✖
                                                        </button>
                                                    </div>
                                                </div>
                                            )}

                                            {file && isPdf && (
                                                <div className="video-preview-box">
                                                    <div className="video-info">
                                                        <span className="video-icon">📄</span>
                                                        <span className="video-name">{file.name}</span>
                                                        <a
                                                            href={previewUrl}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="play-btn"
                                                        >
                                                            View
                                                        </a>
                                                        <button
                                                            type="button"
                                                            className="delete-btn"
                                                            onClick={handleDelete}
                                                        >
                                                            ✖
                                                        </button>
                                                    </div>
                                                </div>
                                            )}

                                            <p className="guidelines">
                                                <span className="red">*</span>{" "}
                                                {contest?.add_info?.trim()
                                                    ? contest.add_info
                                                    : "No additional information available."}
                                            </p>

                                            <div className="form-group">
                                                <label htmlFor="title" className="title-heading">
                                                    Title
                                                </label>
                                                <input
                                                    type="text"
                                                    id="title"
                                                    className="form-control title-input"
                                                    value={title}
                                                    onChange={(e) => {
                                                        setTitle(e.target.value);
                                                        setSubmitError("");
                                                    }}
                                                    disabled={submitting}
                                                    required
                                                />
                                            </div>

                                            <div className="form-group">
                                                <label htmlFor="description" className="description-heading">
                                                    Description
                                                </label>
                                                <textarea
                                                    id="description"
                                                    className="form-control description-input"
                                                    value={description}
                                                    onChange={(e) => {
                                                        setDescription(e.target.value);
                                                        setSubmitError("");
                                                    }}
                                                    disabled={submitting}
                                                    rows="4"
                                                />
                                            </div>

                                            {submitError && (
                                                <div className="alert alert-danger mt-3" role="alert">
                                                    {submitError}
                                                </div>
                                            )}

                                            {submitMessage && (
                                                <div className="alert alert-success mt-3" role="alert">
                                                    {submitMessage}
                                                </div>
                                            )}

                                            <button
                                                type="submit"
                                                className="submit-btn"
                                                disabled={submitting}
                                            >
                                                {submitting
                                                    ? "Uploading..."
                                                    : mySubmission
                                                      ? "Submit again"
                                                      : "Submit"}
                                            </button>
                                        </form>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* File Preview Modal */}
            {showModal && (
                <div className="video-modal">
                    <div className="video-modal-content">
                        <button
                            className="close-btn"
                            onClick={() => setShowModal(false)}
                        >
                            ✖
                        </button>
                        <video controls src={previewUrl} />
                    </div>
                </div>
            )}

            {/* Contest Video Modal */}
            {selectedVideo && (
                <div
                    className="video-modal"
                    onClick={() => setSelectedVideo(null)}
                >
                    <div
                        className="video-modal-content"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <button
                            className="close-btn"
                            onClick={() => setSelectedVideo(null)}
                        >
                            ✖
                        </button>
                        <video
                            controls
                            src={selectedVideo}
                            style={{ width: "100%", maxWidth: "90vw" }}
                        />
                    </div>
                </div>
            )}
        </div>
    );
};

export default CompetitionDetails;
