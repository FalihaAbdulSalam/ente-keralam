import { useEffect, useMemo, useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import { articlesAPI } from "../services/api";
import { useLanguage } from "./LanguageContext";

export default function DeptDetails() {
    const { language } = useLanguage();
    const [activeGroup, setActiveGroup] = useState(0);
    const [article, setArticle] = useState(null);
    const [allArticles, setAllArticles] = useState([]);
    const [searchTerm, setSearchTerm] = useState("");
    const [searchLoading, setSearchLoading] = useState(false);
    const [searchError, setSearchError] = useState(null);

    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === "ml" ? malText || enText : enText;
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

    // Helper to add target="_blank" to all links
    const processLinksForNewTab = (html) => {
        if (!html) return html;
        // Replace all <a href=...> tags with <a href=... target="_blank" rel="noopener noreferrer">
        return html.replace(
            /<a\s+href=/gi,
            '<a target="_blank" rel="noopener noreferrer" href='
        );
    };

    const query = useMemo(
        () => new URLSearchParams(window.location.search),
        []
    );
    const articleId = query.get("id");
    const sectorId =
        article?.sector?.id || article?.sector_details_id || article?.sector_id;

    useEffect(() => {
        async function load() {
            try {
                const [listRes, detailRes] = await Promise.all([
                    articlesAPI.getAll(),
                    articleId
                        ? articlesAPI.getById(articleId)
                        : Promise.resolve(null),
                ]);

                if (listRes?.data?.status && Array.isArray(listRes.data.data)) {
                    setAllArticles(listRes.data.data);
                }
                if (detailRes?.data?.status) {
                    const data = Array.isArray(detailRes.data.data)
                        ? detailRes.data.data[0]
                        : detailRes.data.data;
                    setArticle(data);
                } else if (!articleId && listRes?.data?.status) {
                    setArticle(listRes.data.data[0] || null);
                }
            } catch (e) {
                // no-op
            }
        }
        load();
    }, [articleId]);

    const handleSearch = async (e) => {
        e.preventDefault();
        const term = searchTerm.trim();
        if (!term) return;
        const sector =
            sectorId ||
            (allArticles?.[0]?.sector?.id ??
                allArticles?.[0]?.sector_details_id ??
                1);

        setSearchLoading(true);
        setSearchError(null);
        try {
            const res = await fetch(
                `/api/sectorwise/?keyword=${encodeURIComponent(
                    term
                )}&sector_id=${articleId}`
            );
            const data = await res.json();
            const list = data?.results?.Article || [];
            setAllArticles(list);
            if (list.length) {
                setArticle(list[0]);
            }
        } catch (err) {
            setSearchError("Search failed. Please try again.");
        } finally {
            setSearchLoading(false);
        }
    };

    // Share functions
    const getShareUrl = () => {
        return window.location.href;
    };

    const getShareTitle = () => {
        return getDisplayText(
            article?.entitle || "Ente Keralam",
            article?.maltitle
        );
    };

    const getShareDescription = () => {
        const content = getDisplayText(
            article?.encontent || "",
            article?.malcontent || ""
        );
        // Extract text from HTML and truncate
        const plainText = content.replace(/<[^>]*>/g, "").substring(0, 200);
        return plainText;
    };

    const handleFacebookShare = () => {
        const url = getShareUrl();
        const facebookShareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
            url
        )}`;
        window.open(facebookShareUrl, "_blank", "width=600,height=400");
    };

    const handleTwitterShare = () => {
        const url = getShareUrl();
        const title = getShareTitle();
        const twitterShareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(
            url
        )}&text=${encodeURIComponent(title)}`;
        window.open(twitterShareUrl, "_blank", "width=600,height=400");
    };

    const handleWhatsAppShare = () => {
        const url = getShareUrl();
        const title = getShareTitle();
        const description = getShareDescription();
        const message = `${title}\n\n${description}\n\n${url}`;
        const whatsappShareUrl = `https://wa.me/?text=${encodeURIComponent(
            message
        )}`;
        window.open(whatsappShareUrl, "_blank");
    };

    const handleInstagramShare = () => {
        const url = getShareUrl();
        const title = getShareTitle();
        // Instagram doesn't support direct URL sharing, so we'll copy to clipboard and inform user
        const shareText = `Check this out: ${title}\n${url}`;
        navigator.clipboard
            .writeText(shareText)
            .then(() => {
                alert("Link copied to clipboard! Share it on Instagram.");
            })
            .catch(() => {
                // Fallback: just open Instagram
                window.open("https://www.instagram.com", "_blank");
            });
    };

    const newsletterData = (allArticles || []).slice(0, 6).map((a) => ({
        id: a.id,
        title: getDisplayText(a.entitle, a.maltitle),
        img: a.poster || "/design/assets/newsletter/2.webp",
    }));

    const totalGroups = Math.ceil(newsletterData.length / 3) || 1;

    const processContentLinks = (html) => {
        if (!html) return html;

        let processed = html;

        // 1️⃣ Add target="_blank" to existing <a> tags
        processed = processed.replace(
            /<a\s+(?![^>]*target=)/gi,
            '<a target="_blank" rel="noopener noreferrer" '
        );

        // 2️⃣ Convert plain URLs into clickable links
        processed = processed.replace(
            /(^|[\s>])((https?:\/\/[^\s<]+))/gi,
            '$1<a href="$2" target="_blank" rel="noopener noreferrer">$2</a>'
        );

        return processed;
    };

    return (
        <>
            <section
                id="saasio-breadcurmb"
                className="saasio-breadcurmb-section"
            >
                <div className="container-fluid">
                    <div className="col-md-11 mx-auto">
                        <div className="breadcurmb-title ">
                            <h2>
                                {getDisplayText(
                                    article?.sector?.entitle || "Discussion",
                                    article?.sector?.maltitle
                                )}
                            </h2>
                        </div>
                        <div className="breadcurmb-item-list ul-li">
                            <ul className="saasio-page-breadcurmb">
                                <li>
                                    <a href="#">Home</a>
                                </li>
                                {/* <li>
                                    <a href="#">Discussion</a>
                                </li> */}
                                <li>
                                     <a href="#">{article?.articletype}</a>
                                </li>
                                <li>
                                    <a href="#">  {getDisplayText(
                                    article?.sector?.entitle || "Discussion",
                                    article?.sector?.maltitle
                                )}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
            <section id="news-feed" className="news-feed-section">
                <div className="container-fluid">
                    <div className="col-md-9 mx-auto blog-feed-content">
                        <div className="row">
                            {/* Main Blog Content */}
                            <div className="col-md-8">
                                <div className="saasio-blog-details-content">
                                    {/* Blog Header */}
                                    <div className="blog-details-text dia-headline wow fadeInTop d-flex align-items-center justify-content-between mb-4 pb-3 text-start">
                                        <h1 className="inhead Fword">
                                            <span>
                                                {getDisplayText(
                                                    article?.sector?.entitle ||
                                                        "Sector",
                                                    article?.sector?.maltitle
                                                )}
                                            </span>
                                        </h1>
                                        <div className="sicons m-0">
                                            <small>Share: </small>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleFacebookShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/facebook.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleInstagramShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/insta.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleWhatsAppShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/whatsapp.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleTwitterShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/twitter1.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                        </div>
                                    </div>

                                    {/* Blog Image */}
                                    {/* <div className="blog-details-img mt-3">
                                        <img
                                            src={
                                                article?.banner ||
                                                "/design/assets/cr1.png"
                                            }
                                            alt={article?.entitle || "Blog"}
                                        />
                                    </div> */}

                                    <div className="blog-details-img wow fadeInLeft">
                                        <div className="postr d-flex align-items-center justify-content-between flex-wrap">
                                            {/* {!showQuiz && ( */}
                                            <div className="col-lg-5 d-flex flex-column align-items-center justify-content-center text-center">
                                                <div className="mb-3 d-flex gap-3">
                                                    <h5>
                                                        {getDisplayText(
                                                            article?.entitle ||
                                                                "Content without backward-compatible data.",
                                                            article?.maltitle
                                                        )}
                                                    </h5>
                                                </div>
                                                <div className="saasio-post-meta">
                                                    <a href="#">
                                                        <i className="fas fa-calendar-alt"></i>{" "}
                                                        {formatDate(
                                                            article?.updated_at
                                                        )}
                                                    </a>
                                                    <a href="#">
                                                        <i className="fas fa-user"></i>{" "}
                                                        Admin
                                                    </a>
                                                </div>

                                                <div className="it-nw-btn text-center"></div>
                                            </div>
                                            {/* )} */}
                                            <div className="col-lg-7 p-0">
                                                <img
                                                    src={
                                                        article?.poster
                                                        // "/design/assets/cr1.png"
                                                    }
                                                    // src={"/design/assets/dd.png"}
                                                    alt={getDisplayText(
                                                        article?.entitle ||
                                                            "Blog",
                                                        article?.maltitle
                                                    )}
                                                    className="img-fluid rounded"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Blog Body */}
                                    <div className="blog-details-text dia-headline">
                                        {/* <h2>
                                            {article?.entitle ||
                                                "Content without backward-compatible data."}
                                        </h2> */}

                                        <article
                                            dangerouslySetInnerHTML={{
                                                __html: processContentLinks(
                                                    getDisplayText(
                                                        article?.endescription ||
                                                            "",
                                                        article?.maldescription ||
                                                            ""
                                                    )
                                                ),
                                            }}
                                            className="article-content-with-images"
                                            style={{
                                                wordBreak: "break-word",
                                                overflowWrap: "break-word",
                                            }}
                                        />

                                        <style>{`
                                            .article-content-with-images figure {
                                                margin: 20px 0;
                                                padding: 0;
                                                text-align: center;
                                            }
                                            .article-content-with-images figure img {
                                                max-width: 100%;
                                                height: auto;
                                                display: block;
                                                border-radius: 8px;
                                                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                                            }
                                            .article-content-with-images img {
                                                max-width: 100%;
                                                height: auto;
                                                display: block;
                                                margin: 15px 0;
                                                border-radius: 8px;
                                            }
                                            .article-content-with-images p {
                                                line-height: 1.6;
                                                margin-bottom: 15px;
                                            }
                                            .article-content-with-images a {
                                                color: #0066cc;
                                                text-decoration: none;
                                            }
                                            .article-content-with-images a:hover {
                                                text-decoration: underline;
                                            }
                                        `}</style>
                                    </div>

                                    {/* Tags and Share */}
                                    <div className="blog-details-tag clearfix">
                                        <div className="blog-feed-tag float-left">
                                            <span>Tags:</span>
                                            <a href="#">
                                                {getDisplayText(
                                                    article?.type?.entitle ||
                                                        "Article",
                                                    article?.type?.maltitle
                                                )}
                                            </a>
                                        </div>
                                        <div className="sicons m-0 blog-feed-share float-right">
                                            <span>Share:</span>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleFacebookShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/facebook.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleInstagramShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/insta.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleWhatsAppShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/whatsapp.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                            <a
                                                href="#"
                                                className=""
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleTwitterShare();
                                                }}
                                            >
                                                <img
                                                    src="/design/assets/social/twitter1.svg"
                                                    alt="ente_keralam"
                                                />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Sidebar */}
                            <div className="col-md-4">
                                <div className="saasio-blog-sidebar">
                                    {/* Search Widget */}
                                    <div
                                        className="side-bar-widget wow fadeInRight"
                                        style={{ backgroundColor: "#12111a" }}
                                    >
                                        <div className="search-widget dia-headline">
                                            <form
                                                onSubmit={handleSearch}
                                                className="relative-position"
                                            >
                                                <input
                                                    type="text"
                                                    name="search"
                                                    placeholder="Search Here"
                                                    value={searchTerm}
                                                    onChange={(e) =>
                                                        setSearchTerm(
                                                            e.target.value
                                                        )
                                                    }
                                                    style={{
                                                        backgroundColor:
                                                            "#f9f9f9",
                                                    }}
                                                />
                                                <button
                                                    type="submit"
                                                    disabled={searchLoading}
                                                >
                                                    {searchLoading ? (
                                                        <i className="fas fa-spinner fa-spin"></i>
                                                    ) : (
                                                        <i className="fas fa-search"></i>
                                                    )}
                                                </button>
                                            </form>
                                            {searchError && (
                                                <div className="text-danger mt-2">
                                                    {searchError}
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* Related Articles */}
                                    <div className="side-bar-widget wow fadeInUp">
                                        <div className="category-widget dia-headline ul-li-block">
                                            <h3 className="widget-title-2">
                                                Related Articles
                                            </h3>
                                            <div className="recent-post-area">
                                                {newsletterData.map((item) => (
                                                    <div
                                                        className="recent-post-img-text"
                                                        key={item.id}
                                                    >
                                                        <a
                                                            href={`/dept-detail?id=${item.id}`}
                                                        >
                                                            <div className="recent-post-img float-left">
                                                                {/* <img src={item.img} alt={item.title} /> */}
                                                                <img
                                                                    src="/design/assets/dis.jpg"
                                                                    alt="Related Post"
                                                                />
                                                            </div>
                                                            <div className="recent-post-text dia-headline">
                                                                <h3>
                                                                    <a
                                                                        href={`/dept-detail?id=${item.id}`}
                                                                    >
                                                                        {
                                                                            item.title
                                                                        }
                                                                    </a>
                                                                </h3>
                                                                <span className="rec-post-meta">
                                                                    <a href="#">
                                                                        Read
                                                                    </a>
                                                                </span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Newsletters */}
                                    <div
                                        className="side-bar-widget"
                                        style={{ overflow: "hidden" }}
                                    >
                                        <div className="popular-widget dia-headline ul-li">
                                            <h3 className="widget-title-2">
                                                Newsletters
                                            </h3>
                                            <div
                                                className="it-nx-testimonial-content pt-0 wow fadeInUp"
                                                data-wow-delay="200ms"
                                                data-wow-duration="1500ms"
                                            >
                                                {/* <div className="it-nx-testimonial-slider">
                                                  <div className="it-nw-testimonial-innerbox p-0 position-relative">
                                                      <img src="/design/assets/newsletter/2.webp" alt="Newsletter" />
                                                  </div>
                                              </div> */}
                                                <Swiper
                                                    // modules={[Autoplay]}
                                                    slidesPerView={2}
                                                    spaceBetween={30}
                                                    loop={true}
                                                    autoplay={{
                                                        delay: 2500,
                                                        disableOnInteraction: false,
                                                    }}
                                                    breakpoints={{
                                                        0: { slidesPerView: 1 },
                                                        768: {
                                                            slidesPerView: 2,
                                                        },
                                                        1024: {
                                                            slidesPerView: 2,
                                                        },
                                                    }}
                                                    onSlideChange={(swiper) => {
                                                        const groupIndex =
                                                            Math.floor(
                                                                swiper.realIndex /
                                                                    3
                                                            );
                                                        setActiveGroup(
                                                            groupIndex
                                                        );
                                                    }}
                                                >
                                                    {newsletterData.map(
                                                        (item) => (
                                                            <SwiperSlide
                                                                key={item.id}
                                                            >
                                                                <div className="it-nx-testimonial-slider">
                                                                    <div className="it-nw-testimonial-innerbox p-0 position-relative">
                                                                        <img
                                                                            src={
                                                                                item.img
                                                                            }
                                                                            alt={
                                                                                item.title
                                                                            }
                                                                        />
                                                                    </div>
                                                                </div>
                                                            </SwiperSlide>
                                                        )
                                                    )}
                                                </Swiper>

                                                {/* Custom Pagination Dots */}
                                                <div className="custom-pagination">
                                                    {Array.from({
                                                        length: totalGroups,
                                                    }).map((_, i) => (
                                                        <span
                                                            key={i}
                                                            className={`custom-dot ${
                                                                i ===
                                                                activeGroup
                                                                    ? "active"
                                                                    : ""
                                                            }`}
                                                        ></span>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
