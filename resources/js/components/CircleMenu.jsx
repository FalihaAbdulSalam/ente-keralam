import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import "./CircleMenu.css";

const CircleMenu = () => {
  const [menuOpen, setMenuOpen] = useState(false);
  const [angles, setAngles] = useState([]);
  const [isMobile, setIsMobile] = useState(false);
  const navigate = useNavigate();

  const config = {
    distance: 90, // radius distance from center
    delay: 30, // ms delay between item animations
    itemRotation: 400,
    iconRotation: 180,
  };

  // Updated items with proper navigation links - maps to "View All" pages
  const items = [
    { title: "Pledge", image: "/design/assets/new/c-pledg-pista.svg", link: "/pledge-list", viewAllPage: true, id: "pledge", enabled: true },
    { title: "Poll/Survey", image: "/design/assets/new/c-poll-grn.svg", link: "/polls", viewAllPage: true, id: "tasks", enabled: true },
    { title: "To-Do Task", image: "/design/assets/new/c-task-sky.svg", link: "/list", viewAllPage: true, id: "todo", enabled: false },
    { title: "Discussion", image: "/design/assets/new/c-diss-blue.svg", link: "/list", viewAllPage: true, id: "disscus", enabled: false },
    { title: "Competition", image: "/design/assets/new/c-comp-red.svg", link: "/competition-list", viewAllPage: true, id: "compet", enabled: true },
    { title: "Quiz", image: "/design/assets/new/c-qiz-pink.svg", link: "/quiz-list", viewAllPage: true, id: "quiz", enabled: true },
  ];

  // Compute item angles around a full circle
  useEffect(() => {
    const total = items.length;
    const startAngle = 0;
    const endAngle = 2 * Math.PI; // 360°
    const step = (endAngle - startAngle) / total;

    const newAngles = Array.from({ length: total }, (_, i) => startAngle + i * step);
    setAngles(newAngles);
  }, []);

  useEffect(() => {
    const mq = window.matchMedia("(max-width: 767px)");
    const handleResize = () => setIsMobile(mq.matches);
    handleResize();
    mq.addEventListener("change", handleResize);
    return () => mq.removeEventListener("change", handleResize);
  }, []);

  const toggleMenu = () => setMenuOpen((prev) => !prev);

  const handleItemClick = (e, item) => {
    e.preventDefault();
    e.stopPropagation();
    
    // Simply navigate to the link
    navigate(item.link);
    
    // Close the menu
    setMenuOpen(false);
  };

  return (
    <div
      className={`circle-me ${menuOpen ? "open" : ""} ${
        isMobile ? "mobile-linear" : ""
      }`}
    >
      {/* Center Toggle Button */}
      <div
        className="wcircle-icon"
        aria-label="Menu Toggle"
        style={{
          transform: menuOpen
            ? `translate(-50%, -50%) rotate(${config.iconRotation}deg)`
            : "translate(-50%, -50%) rotate(0deg)",
          transition: "transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)",
        }}
        onClick={toggleMenu}
      >
        
      </div>

      {/* Circle Items */}
      <div className="wcircle-menu">
        {items.map((item, i) => {
          const angle = angles[i] || 0;
          const x = Math.cos(angle) * config.distance;
          const y = Math.sin(angle) * config.distance;

          // Calculate delay based on open or close state
          const openDelay = i * (config.delay / 700);
          const closeDelay = (items.length - 1 - i) * (config.delay / 700);

          return (
            <a
              key={i}
              href={item.enabled ? item.link : "#"}
              className={`wcircle-menu-item ${menuOpen ? "show" : ""} ${!item.enabled ? "disabled" : ""}`}
              data-title={item.title}
              style={{
                transform: menuOpen
                  ? `translate(${x}px, ${y}px) rotate(0deg)`
                  : `translate(0, 0) rotate(${config.itemRotation}deg)`,
                transition: `all 0.4s ease-out ${
                  menuOpen ? openDelay : closeDelay
                }s`,
                textDecoration: "none",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                cursor: item.enabled ? "pointer" : "not-allowed",
                opacity: item.enabled ? (menuOpen ? 1 : 0) : (menuOpen ? 0.5 : 0),
              }}
              onClick={(e) => {
                if (!item.enabled) {
                  e.preventDefault();
                  e.stopPropagation();
                  return;
                }
                handleItemClick(e, item);
              }}
              title={item.title}
            >
              <img src={item.image} alt={item.title} style={{ pointerEvents: "none" }} />
            </a>
          );
        })}
      </div>
    </div>
  );
};

export default CircleMenu;
