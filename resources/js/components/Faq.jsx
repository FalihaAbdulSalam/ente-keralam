import { useState, useEffect } from "react";
import { useLanguage } from "./LanguageContext";

const FaqPage = () => {
    const { language } = useLanguage();
    const [activeIndex, setActiveIndex] = useState(null);
    const [faqs, setFaqs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === 'ml' ? (malText || enText) : enText;
    };

    useEffect(() => {
        fetchFaqs();
    }, []);

    const fetchFaqs = async () => {
        try {
            setLoading(true);
            const response = await fetch(
                "/api/faq"
            );
            const data = await response.json();

            if (data.status && data.data) {
                // Transform API data to support both English and Malayalam
                const transformedFaqs = data.data.map((faq) => ({
                    enquestion: faq.enquestion || '',
                    malanswer: faq.malanswer ? faq.malanswer.replace(/\r\n/g, "</p><p>").replace(/•/g, "<strong>•</strong>") : '',
                    malquestion: faq.malquestion || '',
                    enenswer: faq.enenswer ? faq.enenswer.replace(/\r\n/g, "</p><p>").replace(/•/g, "<strong>•</strong>") : '',
                }));
                setFaqs(transformedFaqs);
            }
            setError(null);
        } catch (err) {
            console.error("Error fetching FAQs:", err);
            setError("Failed to load FAQs");
            // Fallback to empty array if API fails
            setFaqs([]);
        } finally {
            setLoading(false);
        }
    };

    const toggleFAQ = (index) => {
        if (activeIndex === index) {
            setActiveIndex(null);
        } else {
            setActiveIndex(index);
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
                        <h2>FAQ's</h2>
                    </div>
                    <div className="breadcurmb-item-list ul-li">
                        <ul className="saasio-page-breadcurmb">
                            <li>
                                <a href="/">Home</a>
                            </li>
                            <li>
                                <a href="/faq">FAQ's</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <section
                id="news-feed"
                className="news-feed-section position-relative"
            >
                <div className="it-nw-service-sh1 position-absolute">
                    <img src="/design/assets/ker.png" alt="" />
                </div>
                <div className="it-nw-side-bg text-center position-absolute">
                    <img
                        src="/design/assets/background/Component 574.svg"
                        alt=""
                    />
                </div>
                <div className="container">
                    <div className="blog-feed-content">
                        <div className="col-md-12 mx-auto">
                            <div className="saasio-blog-details-content">
                                <div className="blog-details-text dia-headline wow fadeInTop mb-4 pb-3 text-center">
                                    <span className="eg-title-tag">
                                        Know More{" "}
                                        <i className="square-shape">
                                            <i></i>
                                            <i></i>
                                            <i></i>
                                            <i></i>
                                        </i>
                                    </span>
                                    <h1 className="inhead Fword mt-2">
                                        <span>Frequently</span> Asked Question
                                    </h1>
                                </div>

                                <section className="faq-section">
                                    {loading && (
                                        <div className="text-center py-5">
                                            <p>Loading FAQs...</p>
                                        </div>
                                    )}
                                    {error && (
                                        <div className="alert alert-danger" role="alert">
                                            {error}
                                        </div>
                                    )}
                                    {!loading && faqs.length === 0 && !error && (
                                        <div className="text-center py-5">
                                            <p>No FAQs available</p>
                                        </div>
                                    )}
                                    {!loading && faqs.length > 0 && (
                                        <div className="faq">
                                            {faqs.map((faq, index) => (
                                                <div key={index} className="card">
                                                    <div
                                                        className="card-header"
                                                        onClick={() =>
                                                            toggleFAQ(index)
                                                        }
                                                        style={{
                                                            cursor: "pointer",
                                                        }}
                                                    >
                                                        <h5 className="faq-title mb-0">
                                                            <span className="badge">
                                                                {index + 1}
                                                            </span>{" "}
                                                            {getDisplayText(faq.enquestion, faq.malquestion)}
                                                        </h5>
                                                    </div>
                                                    {activeIndex === index && (
                                                        <div className="card-body">
                                                            <div
                                                                dangerouslySetInnerHTML={{
                                                                    __html: `<p>${getDisplayText(faq.enenswer, faq.malanswer)}</p>`,
                                                                }}
                                                            />
                                                        </div>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default FaqPage;
