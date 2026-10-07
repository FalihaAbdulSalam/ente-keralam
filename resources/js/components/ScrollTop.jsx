// import { useState, useEffect } from "react";

// function ScrollTop() {
//   const [visible, setVisible] = useState(false);

//   useEffect(() => {
//     const onScroll = () => {
//       setVisible(window.pageYOffset > 200);
//     };

//     window.addEventListener('scroll', onScroll, { passive: true });
//     // initialize
//     onScroll();

//     return () => window.removeEventListener('scroll', onScroll);
//   }, []);

//   const handleClick = () => {
//     window.scrollTo({ top: 0, behavior: 'smooth' });
//   };

//   return (
//     // <button
//     //   type="button"
//     //   className={`scroll-top ${visible ? 'visible' : ''}`}
//     //   onClick={handleClick}
//     //   aria-label="Scroll to top"
//     // >
//     //   <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
//     //     <path d="M6 15l6-6 6 6" stroke="#fff" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
//     //   </svg>
//     // </button>

//     <div className="up">
//       <a
//         href="#"
//         className={`scrollup text-center ${visible ? 'visible' : ''}`}
//       >
//         <i className="fas fa-chevron-up"></i>
//       </a>
//     </div>

//   );
// }

// export default ScrollTop;
import { useState, useEffect, useRef } from "react";

function ScrollTop() {
    const [visible, setVisible] = useState(false);
    const lastScrollRef = useRef(0);

    useEffect(() => {
        const onScroll = () => {
            const currentScroll = window.pageYOffset;
            // Show when scrolled beyond threshold
            setVisible(currentScroll > 200);
            lastScrollRef.current = currentScroll;
        };

        window.addEventListener("scroll", onScroll, { passive: true });
        // initialize
        onScroll();

        return () => window.removeEventListener("scroll", onScroll);
    }, []);

    const handleClick = (e) => {
        if (e && e.preventDefault) e.preventDefault();
        // smooth scroll to top
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    return (
        // <><button
        //       type="button"
        //       className={`scroll-top ${visible ? "visible" : ""}`}
        //       onClick={handleClick}
        //       aria-label="Scroll to top"
        //   >
        //       <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
        //           <path d="M6 15l6-6 6 6" stroke="#fff" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
        //       </svg>
        //   </button>
        <div className="up">
            <a
                href="#"
                onClick={handleClick}
                className={`scrollup text-center ${visible ? "visible" : ""}`}
            >
                <i className="fas fa-chevron-up"></i>
            </a>
        </div>
        //   </>
    );
}

export default ScrollTop;
