import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useLanguage } from "./LanguageContext";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Carousel } from "react-bootstrap";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import {
    articleTypesAPI,
    sectorsAPI,
    articlesAPI,
    creativethoughtsAPI,
} from "../services/api";
const InsightsPage = () => {
    const { language } = useLanguage();
    const [activeIndex, setActiveIndex] = useState(0);
    const [articleTypes, setArticleTypes] = useState([]);
    const [featuredArticles, setFeaturedArticles] = useState([]);
    const [sectors, setSectors] = useState([]);
    const [galleryItems, setGalleryItems] = useState([]);
    const [loading, setLoading] = useState(true);
    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === "ml" ? malText || enText : enText;
    };

    const handlePrev = () => {
        setActiveIndex(activeIndex === 0 ? 2 : activeIndex - 1);
    };

    const handleNext = () => {
        setActiveIndex(activeIndex === 2 ? 0 : activeIndex + 1);
    };

    const handleSelect = (selectedIndex) => {
        setActiveIndex(selectedIndex);
    };

    const DEFAULT_GALLERY_ITEMS = [
        "/design/assets/b21.png",
        "/design/assets/b22.png",
        "/design/assets/b23.png",
    ];

    useEffect(() => {
        const fetchCreativeThoughts = async () => {
            try {
                const response = await creativethoughtsAPI.getAll();
                if (
                    response.data.status &&
                    response.data.data &&
                    response.data.data.length > 0
                ) {
                    const items = response.data.data.map((item) => item.poster);
                    setGalleryItems(items);
                } else {
                    setGalleryItems(DEFAULT_GALLERY_ITEMS);
                }
            } catch (error) {
                console.error("Error fetching creative thoughts:", error);
                setGalleryItems(DEFAULT_GALLERY_ITEMS);
            } finally {
                setLoading(false);
            }
        };

        fetchCreativeThoughts();
    }, []);

    const items =
        galleryItems.length > 0 ? galleryItems : DEFAULT_GALLERY_ITEMS;

    useEffect(() => {
        async function loadData() {
            try {
                const [typesRes, sectorsRes, articlesRes] = await Promise.all([
                    articleTypesAPI.getAll(),
                    sectorsAPI.getAll(),
                    articlesAPI.getAll(),
                ]);

                if (
                    typesRes?.data?.status &&
                    Array.isArray(typesRes.data.data)
                ) {
                    setArticleTypes(typesRes.data.data);
                }
                if (
                    sectorsRes?.data?.status &&
                    Array.isArray(sectorsRes.data.data)
                ) {
                    setSectors(sectorsRes.data.data);
                }
                if (
                    articlesRes?.data?.status &&
                    Array.isArray(articlesRes.data.data)
                ) {
                    const fa = articlesRes.data.data.filter(
                        (a) =>
                            a?.type?.entitle === "Featured data" ||
                            a?.articletype_id === 2
                    );
                    setFeaturedArticles(fa);
                }
            } catch (e) {
                // console.error('Failed to load insights data', e);
            }
        }
        loadData();
    }, []);

    return (
        <>
            {/* Breadcrumb Section */}
            <section
                id="saasio-breadcurmb"
                className="saasio-breadcurmb-section"
            >
                <div className="container">
                    <div className="breadcurmb-title">
                        <h2>Insights</h2>
                    </div>
                    <div className="breadcurmb-item-list ul-li">
                        <ul className="saasio-page-breadcurmb">
                            <li>
                                <a href="#">Home</a>
                            </li>
                            <li>
                                <a href="#">Insights</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* Blog Section */}
            <section
                id="news-feed"
                className="news-feed-section position-relative"
            >
                <div className="it-nw-side-bg text-center position-absolute">
                    {/* <img src="/design/assets/its-2/side-line.png" alt="" /> */}
                    <img src="/design/assets/background/sides.svg" alt="" />
                </div>
                <div className="container">
                    <div className="blog-feed-content">
                        <div className="col-md-12 mx-auto">
                            <div className="row">
                                <div className="col-md-12">
                                    <div className="saasio-blog-details-content">
                                        {/* Title */}
                                        <div className="blog-details-text dia-headline wow fadeInTop mb-4 pb-3 text-center">
                                            {/* <span className="eg-title-tag">
                        Kerala Insights{" "}
                        <i className="square-shape">
                          <i></i>
                          <i></i> <i></i> <i></i>
                        </i>
                      </span> */}
                                            <h1 className="inhead Fword mt-2">
                                                <span>Kerala</span> at a Glance
                                            </h1>
                                        </div>
                                        <div className="carousel-container">
                                            {loading ? (
                                                <div
                                                    style={{
                                                        width: "100%",
                                                        textAlign: "center",
                                                        padding: "20px",
                                                    }}
                                                >
                                                    Loading gallery...
                                                </div>
                                            ) : (
                                                //  items.map((src, index) => (
                                                <>
                                                    <Carousel
                                                        activeIndex={
                                                            activeIndex
                                                        }
                                                        onSelect={handleSelect}
                                                        interval={2000}
                                                        controls={false}
                                                        indicators={false}
                                                    >
                                                        {items.map(
                                                            (src, index) => (
                                                                <Carousel.Item
                                                                    key={index}
                                                                >
                                                                    <img
                                                                        className="d-block w-100"
                                                                        src={
                                                                            src
                                                                        }
                                                                        alt={`Creative ${
                                                                            index +
                                                                            1
                                                                        }`}
                                                                    />
                                                                </Carousel.Item>
                                                            )
                                                        )}
                                                    </Carousel>
                                                    <a
                                                        className="carousel-control-prev"
                                                        onClick={handlePrev}
                                                    >
                                                        <span
                                                            className="carousel-control-prev-icon"
                                                            aria-hidden="true"
                                                        ></span>
                                                        <span className="sr-only">
                                                            Previous
                                                        </span>
                                                    </a>
                                                    <a
                                                        className="carousel-control-next"
                                                        onClick={handleNext}
                                                    >
                                                        <span
                                                            className="carousel-control-next-icon"
                                                            aria-hidden="true"
                                                        ></span>
                                                        <span className="sr-only">
                                                            Next
                                                        </span>
                                                    </a>
                                                </>
                                                // ))
                                            )}
                                        </div>

                                        <div className="blog-details-text dia-headline mb-40 wow fadeInRight mt-4">
                                            <article className="d-none">
                                                It is a long established fact
                                                that a reader will be distracted
                                                by the readable content of a
                                                page when looking at its layout.
                                                The point of using Lorem Ipsum
                                                The man, who is in a stable
                                                condition in hospital, has
                                                "potentially life-changing
                                                injuries" after the overnight
                                                attack in Garvagh, County Lono
                                                donderry. He was shot in the
                                                arms and legs."What sort of men
                                                would think it is accepttable to
                                                sub ject a young girl to this
                                                level of brutality and violence?
                                            </article>
                                            <article className="d-none">
                                                The point of using Lorem Ipsum
                                                The man, who is in a stable
                                                condition in hospital, has
                                                "potentially life-changing
                                                injuries" after the overnight
                                                attack in Garvagh, County Lono
                                                donderry. He was shot in the
                                                arms and legs."What sort of men
                                                would think it is acceptable to
                                                subject a young girl to this
                                                level of brutality and violence?
                                            </article>
                                            <article className="d-none">
                                                It is a long established fact
                                                that a reader will be distracted
                                                by the readable content of a
                                                page when looking at its layout.
                                                The point of using Lorem Ipsum
                                                The man, who is in a stable
                                                condition in hospital, has
                                                "potentially life-changing
                                                injuries" after the overnight
                                                attack in Garvagh, County Lono
                                                donderry. He was shot in the
                                                arms and legs."What sort of men
                                                would think it is acceptable to
                                                subject a young girl to this
                                                level of brutality and violence?
                                            </article>
                                        </div>

                                        {/* Featured Data Stories */}
                                        <div className="mb-40">
                                            <h3 className="subN">
                                                Featured data Stories
                                            </h3>
                                            <div className="row">
                                                {(featuredArticles || [])
                                                    .slice(0, 6)
                                                    .map((item) => (
                                                        <div
                                                            className="col-md-4"
                                                            key={item.id}
                                                        >
                                                            <div className="card chartt p-2">
                                                                <Link
                                                                    to={`/dept-detail?id=${item.id}`}
                                                                >
                                                                    <img
                                                                        src={
                                                                            item.poster ||
                                                                            "/design/assets/gh.png"
                                                                        }
                                                                        className="w-100 mb-3"
                                                                        alt={getDisplayText(
                                                                            item.entitle ||
                                                                                "Featured data",
                                                                            item.maltitle
                                                                        )}
                                                                    />
                                                                </Link>
                                                                <div>
                                                                    <Link
                                                                        to={`/dept-detail?id=${item.id}`}
                                                                        className="ctext"
                                                                    >
                                                                        {getDisplayText(
                                                                            item.entitle,
                                                                            item.maltitle
                                                                        )}
                                                                    </Link>
                                                                    <div className="apldg-blog-meta1">
                                                                        <Link
                                                                            to={`/dept-detail?id=${item.id}`}
                                                                            className="apldg-blog-date"
                                                                        >
                                                                            Read
                                                                            More
                                                                        </Link>
                                                                        <div className=" float-right insight-img">
                                                                            <small>
                                                                                Share:
                                                                            </small>
                                                                            <a href="#">
                                                                                <img
                                                                                    src="/design/assets/social/facebook.svg"
                                                                                    width="22"
                                                                                    alt="facebook"
                                                                                />
                                                                            </a>
                                                                            <a href="#">
                                                                                <img
                                                                                    src="/design/assets/social/insta.svg"
                                                                                    width="22"
                                                                                    alt="instagram"
                                                                                />
                                                                            </a>
                                                                            <a href="#">
                                                                                <img
                                                                                    src="/design/assets/social/whatsapp.svg"
                                                                                    width="22"
                                                                                    alt="whatsapp"
                                                                                />
                                                                            </a>
                                                                            <a href="#">
                                                                                <img
                                                                                    src="/design/assets/social/twitter.svg"
                                                                                    width="22"
                                                                                    alt="twitter"
                                                                                />
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ))}
                                            </div>
                                        </div>

                                        {/* Sector-Wise Insights */}
                                        <div>
                                            <h3 className="subN mb-30">
                                                Sector-Wise Insights
                                            </h3>
                                            <div className="row dept">
                                                {/* {(sectors || []).map(
                                                    (sector) => {
                                                        const slug = (
                                                            sector.entitle ||
                                                            sector.maltitle ||
                                                            ""
                                                        )
                                                            .toLowerCase()
                                                            .replace(
                                                                /\s+/g,
                                                                "-"
                                                            );
                                                        const isTourism =
                                                            slug === "tourism";

                                                        const Wrapper =
                                                            isTourism
                                                                ? "div"
                                                                : Link;
                                                        const wrapperProps =
                                                            isTourism
                                                                ? {}
                                                                : {
                                                                      to: `/${slug}`,
                                                                  };

                                                        return (
                                                            <div
                                                                className="col-md-4"
                                                                key={sector.id}
                                                            >
                                                                <div className="soft-m-feature-inner position-relative wow fadeFromUp">
                                                                    <Link
                                                                        to={`/${slug}`}
                                                                    >
                                                                        <div className="soft-m-inner-icon">
                                                                            <div className="soft-m-feature-icon text-center">
                                                                                <img
                                                                                    src={
                                                                                        sector.icon ||
                                                                                        "/design/assets/health.gif"
                                                                                    }
                                                                                    className="deptimg"
                                                                                    alt={getDisplayText(
                                                                                        sector.entitle,
                                                                                        sector.maltitle
                                                                                    )}
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                        <div className="soft-m-feature-box">
                                                                            <div className="soft-m-feature-text soft-m-headline pera-content">
                                                                                <h3>
                                                                                    <span>
                                                                                        {getDisplayText(
                                                                                            sector.entitle,
                                                                                            sector.maltitle
                                                                                        )}
                                                                                    </span>
                                                                                </h3>
                                                                                <p
                                                                                    dangerouslySetInnerHTML={{
                                                                                        __html: getDisplayText(
                                                                                            sector.endescription ||
                                                                                                "",
                                                                                            sector.maldescription ||
                                                                                                ""
                                                                                        ),
                                                                                    }}
                                                                                />
                                                                                {!isTourism && (
                                                                                    <Link
                                                                                        className="soft-f-more"
                                                                                        to={`/${slug}`}
                                                                                    >
                                                                                        Read
                                                                                        More
                                                                                    </Link>
                                                                                )}
                                                                            </div>
                                                                        </div>
                                                                    </Link>
                                                                </div>
                                                            </div>
                                                        );
                                                    }
                                                )} */}

                                                {(sectors || []).map((sector) => {
  const slug = (sector.entitle || sector.maltitle || '')
    .toLowerCase()
    .replace(/\s+/g, '-');

  const isTourism = slug === 'tourism';

  const Wrapper = isTourism ? 'div' : Link;
  const wrapperProps = isTourism ? {} : { to: `/${slug}` };

  return (
    <div className="col-md-4" key={sector.id}>
      <div className="soft-m-feature-inner position-relative wow fadeFromUp">
        <Wrapper {...wrapperProps}>
          <div className="soft-m-inner-icon">
            <div className="soft-m-feature-icon text-center">
              <img
                src={sector.icon || "/design/assets/health.gif"}
                className="deptimg"
                alt={getDisplayText(sector.entitle, sector.maltitle)}
              />
            </div>
          </div>

          <div className="soft-m-feature-box">
            <div className="soft-m-feature-text soft-m-headline pera-content">
              <h3>
                <span>{getDisplayText(sector.entitle, sector.maltitle)}</span>
              </h3>

              <p
                dangerouslySetInnerHTML={{
                  __html: getDisplayText(
                    sector.endescription || '',
                    sector.maldescription || ''
                  ),
                }}
              />

              {!isTourism && (
                <Link className="soft-f-more" to={`/${slug}`}>
                  Read More
                </Link>
              )}
            </div>
          </div>
        </Wrapper>
      </div>
    </div>
  );
})}

                                            </div>
                                        </div>
                                        {/* End Sector-Wise Insights */}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default InsightsPage;
