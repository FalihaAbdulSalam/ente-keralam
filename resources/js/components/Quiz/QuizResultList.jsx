import { useState, useEffect } from "react";
import { Carousel } from "react-bootstrap";
import jsPDF from "jspdf";
import { quizAPI } from "../../services/api";

export default function QuizResultListing() {
  const [index, setIndex] = useState(0);
  const [quizzes, setQuizzes] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch quiz results from API
    const fetchQuizResults = async () => {
      try {
        const response = await quizAPI.getResults();
        setQuizzes(response.data.quizzes || response.data || []);
      } catch (error) {
        console.error("Error fetching quiz results:", error);
        // Fallback to sample data if API fails
        setQuizzes([
          {
            id: 1,
            title: "National Space Day Quiz 2025",
            from: "SEP 12,2025",
            to: "SEP 20,2025",
            image: "/design/assets/fp/1_നവകേരളം ക്വിസ് 2025.jpg",
            questions: 10,
            duration: 300,
          },
          {
            id: 2,
            title: "AI Awareness Quiz 2025",
            from: "AUG 01,2025",
            to: "AUG 07,2025",
            image: "/design/assets/fp/2_അതിദരിദ്രരില്ലാത്ത കേരളം  - ക്വിസ്.jpg",
            questions: 15,
            duration: 400,
          },
          {
            id: 3,
            title: "Cyber Safety Quiz",
            from: "JUL 10,2025",
            to: "JUL 20,2025",
            image: "/design/assets/fp/3_ഡിജിറ്റൽ കേരളം ക്വിസ്.jpg",
            questions: 20,
            duration: 600,
          },
          {
            id: 4,
            title: "Green Energy Quiz",
            from: "JUN 15,2025",
            to: "JUN 22,2025",
            image: "/design/assets/fp/4_കുടുംബശ്രീ ക്വിസ്.jpg",
            questions: 8,
            duration: 180,
          },
          {
            id: 5,
            title: "Tech Innovation Quiz",
            from: "MAY 05,2025",
            to: "MAY 12,2025",
            image: "/design/assets/fp/5_ഹരിതകർമസേന ക്വിസ്.jpg",
            questions: 12,
            duration: 360,
          },
        ]);
      } finally {
        setLoading(false);
      }
    };

    fetchQuizResults();
  }, []);

  const handleSelect = (selectedIndex) => {
    setIndex(selectedIndex);
  };

  const images = [
    { src: "/design/assets/qs11.png", alt: "First slide" },
    { src: "/design/assets/qs22.png", alt: "Second slide" },
    { src: "/design/assets/qs3.png", alt: "Third slide" },
  ];

  const handleResultClick = async (quiz) => {
    try {
      // Fetch detailed result data from API
      const response = await quizAPI.getResultById(quiz.id);
      const resultData = response.data;

      // ✅ 1. Generate "Result" PDF (for download)
      const resultPDF = new jsPDF();
      resultPDF.setFontSize(18);
      resultPDF.text("Quiz Result", 20, 20);
      resultPDF.setFontSize(12);
      resultPDF.text(`Title: ${quiz.title}`, 20, 40);
      resultPDF.text(`From: ${quiz.from}`, 20, 50);
      resultPDF.text(`To: ${quiz.to}`, 20, 60);
      resultPDF.text(`Total Questions: ${quiz.questions}`, 20, 70);
      resultPDF.text(`Duration: ${quiz.duration} seconds`, 20, 80);
      resultPDF.text("Status: Completed ✅", 20, 100);

      // Save the result PDF
      resultPDF.save(`${quiz.title.replace(/\s+/g, "_")}_Result.pdf`);

      // ✅ 2. Generate "Name List" PDF (and open in new tab)
      const listPDF = new jsPDF();
      listPDF.setFontSize(18);
      listPDF.text("Top Participants", 20, 20);
      listPDF.setFontSize(12);

      // Use data from API or fallback to sample data
      const names = resultData.participants || [
        "Aisha K.",
        "Rahul P.",
        "Meera S.",
        "Anand T.",
        "Divya L.",
        "Kiran V.",
      ];

      let y = 40;
      names.forEach((name, index) => {
        const displayName = typeof name === 'string' ? name : name.name;
        listPDF.text(`${index + 1}. ${displayName}`, 20, y);
        y += 10;
      });

      // Open name list PDF in new tab
      const pdfBlob = listPDF.output("blob");
      const pdfUrl = URL.createObjectURL(pdfBlob);
      window.open(pdfUrl, "_blank");
    } catch (error) {
      console.error("Error generating result:", error);
      
      // Fallback to basic PDF generation
      const resultPDF = new jsPDF();
      resultPDF.setFontSize(18);
      resultPDF.text("Quiz Result", 20, 20);
      resultPDF.setFontSize(12);
      resultPDF.text(`Title: ${quiz.title}`, 20, 40);
      resultPDF.text(`From: ${quiz.from}`, 20, 50);
      resultPDF.text(`To: ${quiz.to}`, 20, 60);
      resultPDF.text(`Total Questions: ${quiz.questions}`, 20, 70);
      resultPDF.text(`Duration: ${quiz.duration} seconds`, 20, 80);
      resultPDF.text("Status: Completed ✅", 20, 100);
      resultPDF.save(`${quiz.title.replace(/\s+/g, "_")}_Result.pdf`);
    }
  };

  return (
    <>
      <div
        id="carouselExampleIndicators"
        className="carousel slide"
        data-ride="carousel"
      >
        {/* Custom OL LI Indicators */}
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
              <img className="d-block w-100" src={image.src} alt={image.alt} />
            </Carousel.Item>
          ))}
        </Carousel>

        {/* Carousel Controls */}
        <a
          className="carousel-control-prev"
          href="#"
          role="button"
          data-slide="prev"
          onClick={(e) => {
            e.preventDefault();
            setIndex((prevIndex) =>
              prevIndex === 0 ? images.length - 1 : prevIndex - 1
            );
          }}
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
          onClick={(e) => {
            e.preventDefault();
            setIndex((prevIndex) =>
              prevIndex === images.length - 1 ? 0 : prevIndex + 1
            );
          }}
        >
          <span
            className="carousel-control-next-icon"
            aria-hidden="true"
          ></span>
          <span className="sr-only">Next</span>
        </a>
      </div>

      <div className="main-sec">
        <section
          className="listing apldg-blog-section"
          style={{ backgroundColor: "#EEEEEE" }}
        >
          <div className="it-nw-side-bg text-center position-absolute">
            <img src="/design/assets/background/Component 574.svg" alt="" />
          </div>

          <div
            className="container-fluid"
            style={{ zIndex: 6, position: "relative" }}
          >
            <div className="col-md-11 mx-auto">
              <div className="row">
                <div className="apldg-blog-right wow fadeInRight">
                  {loading ? (
                    <div className="text-center py-5">
                      <div className="spinner-border text-primary" role="status">
                        <span className="sr-only">Loading...</span>
                      </div>
                      <p className="mt-3">Loading quiz results...</p>
                    </div>
                  ) : quizzes.length === 0 ? (
                    <div className="text-center py-5">
                      <p>No quiz results available.</p>
                    </div>
                  ) : (
                    <div className="row">{quizzes.map((quiz) => (
                      <div key={quiz.id} className="col-md-4 col-lg-3">
                        <div className="apldg-blog-column qs_block open bg-white">
                          {/* Image */}
                          <div className="apldg-img-wrapper">
                            <img src={quiz.image} alt={quiz.title} />
                          </div>

                          {/* Title */}
                          <div className="apldg-headline mb-2">
                            <a href="#">
                              <h5>{quiz.title}</h5>
                            </a>
                          </div>

                          {/* Dates + Share icons */}
                          <div className="statusShar">
                            <div className="d-flex">
                              <h6>
                                From{" "}
                                <span className="str">:&nbsp;{quiz.from}</span>
                              </h6>
                              <h6 className="ml-3">
                                To{" "}
                                <span className="endd">:&nbsp;{quiz.to}</span>
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
                                <g id="Layer_2" data-name="Layer 2">
                                  <g id="_01.facebook" data-name="01.facebook">
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
                                <div className="qcount">{quiz.questions}</div>
                                Questions
                              </div>
                              <div className="quiz_time">
                                <div className="time_duration">
                                  {quiz.duration} <small>sec</small>
                                </div>
                                Duration
                              </div>
                            </div>
                            <a
                              className="quizresult-btn"
                              onClick={() => handleResultClick(quiz)}
                            >
                              Result
                            </a>
                          </div>
                        </div>
                      </div>
                    ))}
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </>
  );
}
