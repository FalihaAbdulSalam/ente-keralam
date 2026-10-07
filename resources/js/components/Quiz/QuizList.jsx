"use client";
import { useEffect, useState } from "react";
import { Carousel } from "react-bootstrap";
import { Link } from "react-router-dom";
import Select from "react-select";
import { useLanguage } from "../LanguageContext";

export default function QuizListing() {
    const { language } = useLanguage();
    const [index, setIndex] = useState(0);
    const [filter, setFilter] = useState("all");
    const [department, setDepartment] = useState("");
    const [sortBy, setSortBy] = useState("");
    const [quizzes, setQuizzes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === "ml" ? malText || enText : enText;
    };

    const shapes = [
        "Vector (1).svg",
        "Vector (2).svg",
        "Vector (5).svg",
        "Vector (3).svg",
        "Vector (4).svg",
    ];
    const shapeSizes = {
        "Vector (1).svg": 10,
        "Vector (2).svg": 10,
        "Vector (3).svg": 8,
        "Vector (4).svg": 16,
        "Vector (5).svg": 12,
    };

    useEffect(() => {
        const fetchQuizzes = async () => {
            try {
                setLoading(true);
                const response = await fetch("/api/allquizdata");
                const json = await response.json();

                if (json?.data && Array.isArray(json.data)) {
                    const formattedQuizzes = json.data.map((quiz) => ({
                        id: quiz.quiz_id,
                        title: quiz.topic || quiz.name,
                        from: quiz.start_date,
                        to: quiz.end_date,
                        image: quiz.poster
                            ? `${quiz.poster}`
                            : `/design/assets/fp/1_നവകേരളം ക്വിസ് 2025.jpg`,
                        // image:`/design/assets/fp/1_നവകേരളം ക്വിസ് 2025.jpg`,
                        questions: quiz.total_questions,
                        duration: quiz.timer * quiz.total_questions,
                        status:
                            new Date(quiz.end_date) > new Date()
                                ? "open"
                                : "closed",
                    }));
                    setQuizzes(formattedQuizzes);
                }
            } catch (err) {
                console.error("Error fetching quizzes:", err);
                setError("Failed to load quizzes");
            } finally {
                setLoading(false);
            }
        };

        fetchQuizzes();
    }, []);
    const sortByOptions = [
        { value: "newest", label: "Newest First" },
        { value: "oldest", label: "Oldest First" },
        { value: "popular", label: "Most Popular" },
        { value: "result", label: "Result" },
    ];

    const handleSelect = (selectedIndex) => {
        setIndex(selectedIndex);
    };

    const images = [
        { src: "/design/assets/qs11.png", alt: "First slide" },
        { src: "/design/assets/qs22.png", alt: "Second slide" },
        { src: "/design/assets/qs3.png", alt: "Third slide" },
    ];
    return (
        <>
            {/* <div
                id="carouselExampleIndicators"
                className="carousel slide"
                data-ride="carousel"
            >
                <ol className="carousel-indicators">
                    {images.map((_, i) => (
                        <li
                            key={i}
                            data-slide-to={i}
                            className={i === index ? "active" : ""}
                            onClick={() => handleSelect(i)}
                            style={{ cursor: "pointer" }}
                        ></li>
                    ))}
                </ol>

               
                <Carousel
                    activeIndex={index}
                    onSelect={handleSelect}
                    interval={2000}
                    controls={false}
                    indicators={false}
                >
                    {images.map((image, i) => (
                        <Carousel.Item key={i}>
                            <img
                                className="d-block w-100"
                                src={image.src}
                                alt={image.alt}
                            />
                        </Carousel.Item>
                    ))}
                </Carousel>

                <a
                    className="carousel-control-prev"
                    href="#"
                    role="button"
                    data-slide="prev"
                    onClick={() =>
                        setIndex((prevIndex) =>
                            prevIndex === 0 ? images.length - 1 : prevIndex - 1
                        )
                    }
                >
                    <span
                        className="carousel-control-prev-icon"
                        aria-hidden="true"
                    ></span>
                    <span className="sr-only">Previous</span>
                </a>
                <a
                    className="carousel-control-next"
                    href="#"
                    role="button"
                    data-slide="next"
                    onClick={() =>
                        setIndex((prevIndex) =>
                            prevIndex === images.length - 1 ? 0 : prevIndex + 1
                        )
                    }
                >
                    <span
                        className="carousel-control-next-icon"
                        aria-hidden="true"
                    ></span>
                    <span className="sr-only">Next</span>
                </a>
            </div> */}

            <section
                id="saasio-breadcurmb"
                className="saasio-breadcurmb-section"
            >
                <div className="container-fluid">
                    <div className="col-md-11 mx-auto">
                        <div className="breadcurmb-title">
                            <h2>Quiz</h2>
                        </div>
                        <div className="breadcurmb-item-list ul-li">
                            <ul className="saasio-page-breadcurmb">
                                <li>
                                    <a href="/">Home</a>
                                </li>
                                <li>
                                    <a href="#">Quiz</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <div className="main-sec">
                <section
                    className="listing apldg-blog-section"
                    style={{ backgroundColor: "#EEEEEE" }}
                >
                    <div className="blog-shapes-grid-right">
                        {Array.from({ length: 200 }).map((_, i) => {
                            const file = shapes[i % shapes.length];
                            return (
                                <div
                                    className={`it-nw-blog-sh-bg sh${
                                        (i % 3) + 1
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

                    <div
                        className="container-fluid"
                        style={{ zIndex: 6, position: "relative" }}
                    >
                        <div className="col-md-11 mx-auto">
                            {/* <div className=" top-search1 margin-bottom-20">
                <div className="col-lg-6 col-md-4 col-12">
                  <div className="d-flex align-items-center">
                    <div className="form-check mr-2">
                      <input
                        type="radio"
                        className="form-check-input"
                        id="quiz-status-all"
                        name="status"
                        checked={filter === "all"}
                        onChange={() => setFilter("all")}
                      />
                      <label className="form-check-label color" htmlFor="quiz-status-all">All</label>
                    </div>
                    <div className="form-check mr-2">
                      <input
                        type="radio"
                        className="form-check-input"
                        id="quiz-status-open"
                        name="status"
                        checked={filter === "open"}
                        onChange={() => setFilter("open")}
                      />
                      <label className="form-check-label color" htmlFor="quiz-status-open">Open</label>
                    </div>
                    <div className="form-check mr-2">
                      <input
                        type="radio"
                        className="form-check-input"
                        id="quiz-status-closed"
                        name="status"
                        checked={filter === "closed"}
                        onChange={() => setFilter("closed")}
                      />
                      <label className="form-check-label color" htmlFor="quiz-status-closed">Closed</label>
                    </div>
                  </div>
                </div>
                <div className="col-lg-3 col-md-4 col-12">
                  <form
                    className="relative-position search-bx"
                    onSubmit={(e) => e.preventDefault()}
                  >
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Search..."
                      value={department}
                      onChange={(e) => setDepartment(e.target.value)}
                    />
                    <button type="submit">
                      <i className="fas fa-search"></i>
                    </button>
                  </form>
                </div>

                <div className="col-lg-3 col-md-4 col-12">
                  <form className="relative-position search-bx">
                    <Select
                      id="sortBy"
                      value={sortByOptions.find(
                        (option) => option.value === sortBy
                      )}
                      onChange={(selectedOption) =>
                        setSortBy(selectedOption.value)
                      }
                      options={sortByOptions}
                      placeholder="Sort By"
                      classNamePrefix="react-select"
                      styles={{
                        control: (base) => ({
                          ...base,
                          borderRadius: "6px",
                          borderColor: "#ccc",
                          boxShadow: "none",
                          "&:hover": { borderColor: "#999" },
                        }),
                        indicatorSeparator: () => ({
                          display: "none", // removes the separator line
                        }),
                      }}
                    />
                  </form>
                </div>
              </div> */}

                            <div className="row top-search1">
                                <div className="col-lg-6 col-md-4 col-12">
                                    <div className="d-flex align-items-center margin-search">
                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                id="poll-status-all"
                                                name="status"
                                                checked={filter === "all"}
                                                onChange={() =>
                                                    setFilter("all")
                                                }
                                            />
                                            <label
                                                className="form-check-label color"
                                                htmlFor="poll-status-all"
                                            >
                                                All
                                            </label>
                                        </div>
                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                id="poll-status-open"
                                                name="status"
                                                checked={filter === "open"}
                                                onChange={() =>
                                                    setFilter("open")
                                                }
                                            />
                                            <label
                                                className="form-check-label color"
                                                htmlFor="poll-status-open"
                                            >
                                                Open
                                            </label>
                                        </div>
                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                id="poll-status-closed"
                                                name="status"
                                                checked={filter === "closed"}
                                                onChange={() =>
                                                    setFilter("closed")
                                                }
                                            />
                                            <label
                                                className="form-check-label color"
                                                htmlFor="poll-status-closed"
                                            >
                                                Closed
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-lg-3 col-md-4 col-12">
                                    <form
                                        className="relative-position search-bx"
                                        onSubmit={(e) => e.preventDefault()}
                                    >
                                        <input
                                            type="text"
                                            className="form-control"
                                            placeholder="Search..."
                                            value={department}
                                            onChange={(e) =>
                                                setDepartment(e.target.value)
                                            }
                                        />
                                        <button type="submit">
                                            <i className="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>

                                <div className="col-lg-3 col-md-4 col-12">
                                    <form className="relative-position search-bx">
                                        <Select
                                            id="sortBy"
                                            value={sortByOptions.find(
                                                (option) =>
                                                    option.value === sortBy
                                            )}
                                            onChange={(selectedOption) =>
                                                setSortBy(selectedOption.value)
                                            }
                                            options={sortByOptions}
                                            placeholder="Sort By"
                                            classNamePrefix="react-select"
                                            styles={{
                                                control: (base) => ({
                                                    ...base,
                                                    borderRadius: "6px",
                                                    borderColor: "#ccc",
                                                    boxShadow: "none",
                                                    "&:hover": {
                                                        borderColor: "#999",
                                                    },
                                                }),
                                                indicatorSeparator: () => ({
                                                    display: "none", // removes the separator line
                                                }),
                                            }}
                                        />
                                    </form>
                                </div>
                            </div>

                            <div className="">
                                <div className="apldg-blog-right wow fadeInRight">
                                    <div className="row">
                                        {quizzes.map((quiz) => (
                                            <div
                                                key={quiz.id}
                                                className="col-md-4 col-lg-3"
                                            >
                                                <div className="apldg-blog-column qs_block open bg-white">
                                                    {/* Image */}
                                                    <div className="apldg-img-wrapper">
                                                        <img
                                                            src={quiz.image}
                                                            alt={quiz.title}
                                                        />
                                                    </div>

                                                    {/* Title */}
                                                    <div className="apldg-headline mb-2">
                                                        <a href="#">
                                                            <h5>
                                                                {quiz.title}
                                                            </h5>
                                                        </a>
                                                    </div>

                                                    {/* Dates + Share icons */}
                                                    <div className="statusShar">
                                                        <div className="d-flex">
                                                            <h6>
                                                                From{" "}
                                                                <span className="str">
                                                                    :&nbsp;
                                                                    {quiz.from}
                                                                </span>
                                                            </h6>
                                                            <h6 className="ml-3">
                                                                To{" "}
                                                                <span className="endd">
                                                                    :&nbsp;
                                                                    {quiz.to}
                                                                </span>
                                                            </h6>
                                                        </div>

                                                        <div className="sheir">
                                                            {/* Facebook SVG */}
                                                            <svg
                                                                height="22"
                                                                className="fb0"
                                                                viewBox="0 0 176 176"
                                                                width="22"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                            >
                                                                <g
                                                                    id="Layer_2"
                                                                    data-name="Layer 2"
                                                                >
                                                                    <g
                                                                        id="_01.facebook"
                                                                        data-name="01.facebook"
                                                                    >
                                                                        <path
                                                                            id="icon"
                                                                            d="m88 0a88 88 0 1 0 88 88 88 88 0 0 0 -88-88zm27.88 77.59-1.77 15.32a2.86 2.86 0 0 1 -2.82 2.57h-16l-.08 45.45a2.05 2.05 0 0 1 -2 2.07h-16.21a2 2 0 0 1 -2-2.08v-45.44h-12a2.87 2.87 0 0 1 -2.84-2.9l-.06-15.33a2.88 2.88 0 0 1 2.84-2.92h12.06v-14.8c0-17.18 10.2-26.53 25.16-26.53h12.26a2.88 2.88 0 0 1 2.85 2.92v12.91a2.88 2.88 0 0 1 -2.85 2.92h-7.52c-8.13 0-9.71 4-9.71 9.77v12.81h17.87a2.89 2.89 0 0 1 2.82 3.26z"
                                                                        />
                                                                    </g>
                                                                </g>
                                                            </svg>

                                                            {/* Instagram SVG */}
                                                            <svg
                                                                height="22"
                                                                className="inta0"
                                                                viewBox="0 0 512 512"
                                                                width="22"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                data-name="Layer 1"
                                                            >
                                                                <path d="m256 200.0165c34.62 0 62.6843 27.6308 62.6843 61.7148 0 34.0771-28.0645 61.708-62.6843 61.708s-62.6847-27.6309-62.6847-61.708c0-34.084 28.065-61.7148 62.6847-61.7148zm0-24.6778c-48.4676 0-87.7585 38.6778-87.7585 86.3926 0 47.708 39.2909 86.3926 87.7585 86.3926s87.7581-38.6846 87.7581-86.3926c0-47.7148-39.29-86.3926-87.7581-86.3926zm90.4681-20.7676a24.6876 24.6876 0 1 0 25.0742 24.6846 24.8828 24.8828 0 0 0 -25.0742-24.6846zm-151.7256-26.8652h122.515c39.8565 0 72.167 31.8076 72.167 71.0391v125.9658c0 39.2383-32.3105 71.0391-72.167 71.0391h-122.515c-39.8565 0-72.167-31.8008-72.167-71.0391v-125.9658c0-39.2315 32.31-71.0391 72.167-71.0391zm-11.5548-24.5684c-47.3748 0-85.78 37.81-85.78 84.4444v148.292c0 46.6416 38.4047 84.4443 85.78 84.4443h145.6251c47.3743 0 85.779-37.8027 85.779-84.4443v-148.292c0-46.6348-38.4047-84.4444-85.779-84.4444zm72.8123-89.2089c136.8563 0 247.8 110.94 247.8 247.8027 0 136.8555-110.9435 247.7959-247.8 247.7959s-247.8-110.9404-247.8-247.7959c0-136.8623 110.9435-247.8027 247.8-247.8027z" />
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    {/* Quiz Info */}
                                                    <div className="ques-block">
                                                        <div className="question_detail">
                                                            <div className="no_of_ques">
                                                                <div className="qcount">
                                                                    {
                                                                        quiz.questions
                                                                    }
                                                                </div>
                                                                Questions
                                                            </div>
                                                            <div className="quiz_time">
                                                                <div className="time_duration">
                                                                    {isNaN(
                                                                        quiz.duration
                                                                    )
                                                                        ? "0"
                                                                        : quiz.duration}{" "}
                                                                    <small>
                                                                        sec
                                                                    </small>
                                                                </div>
                                                                Duration
                                                            </div>
                                                        </div>
                                                        {/* <Link to={`/quiz-details/${quiz.id}`}> */}
                                                        <a
                                                            href={`/quiz-details/${quiz.id}`}
                                                            className="quizplay-btn"
                                                        >
                                                            Play
                                                        </a>
                                                        {/* </Link> */}
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}
