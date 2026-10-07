import { useEffect, useState } from "react";
import { jsPDF } from "jspdf";
import { useLocation, useNavigate } from "react-router-dom";
import confetti from "canvas-confetti";
import { useAuth } from "../App";
import { generatePledgeCertificate } from "../../utils/certificateGenerator";
import "../Quiz/QuizDetails.css";

const ResultPledgePage = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const { user } = useAuth();
  const [isGeneratingCert, setIsGeneratingCert] = useState(false);
  
  const pledge = location.state?.pledge || null;
  // Use authenticated user's name, or fallback to location state, or default
  const userName = user?.name || location.state?.userName || "Participant";
  const eventName = pledge?.title || "Pledge";
  const participationPoints = pledge?.score ?? 0;
  console.log(eventName, "eventName");
  console.log("User name for certificate:", userName);

  useEffect(() => {
    confetti({
      particleCount: 180,
      spread: 90,
      origin: { y: 0.6 },
    });
  }, []);

 const handleDownloadCertificate = () => {
  const doc = new jsPDF({
    orientation: "landscape",
    unit: "px",
    format: [1200, 800],
  });

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

  // 🔤 Font and background paths
  const notoMalayalamFontUrl = "/font/NotoSansMalayalam.ttf";
  const openSansFontUrl = "/font/OpenSans.ttf";
  const meaFontUrl = "/font/MeaCulpa.ttf";
  const bgUrl = "/design/assets/certificate/certifiacte-participation.jpg";

  // 🧩 Helper: Draw Malayalam with shaping using browser Canvas
  const drawMalayalamTextAsImage = async (text, color = "#000000") => {
    // ✅ Load font dynamically into browser memory
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

    // Create temporary canvas
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

  // 🧠 Load assets
  Promise.all([
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
      const img = new Image();
      img.crossOrigin = "anonymous";
      img.onload = () => resolve(img);
      img.onerror = reject;
      img.src = bgUrl;
    }),
  ])
    .then(async ([notoMalayalamBuf, meaBuf, openSansBuf, img]) => {
      // ✅ Register fonts in jsPDF (for English fonts)
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

      // 🖼️ Draw background
      const pageW = doc.internal.pageSize.getWidth();
      const pageH = doc.internal.pageSize.getHeight();

      const canvas = document.createElement("canvas");
      canvas.width = img.width;
      canvas.height = img.height;
      const ctx = canvas.getContext("2d");
      ctx.drawImage(img, 0, 0);
      const bgDataUrl = canvas.toDataURL("image/jpeg");
      doc.addImage(bgDataUrl, "JPEG", 0, 0, pageW, pageH);

      // 🧠 Malayalam detection
      const isMalayalam = /[\u0D00-\u0D7F]/.test(eventName);

      if (isMalayalam) {
        // ✅ Render Malayalam correctly via canvas
        const { dataUrl, width } = await drawMalayalamTextAsImage(eventName);
        const displayWidth = Math.max(width, 300);
        const x = pageW / 2 - displayWidth / 2;
        const y = pageH * 0.75;
        doc.addImage(dataUrl, "PNG", x, y - 30, displayWidth, 60);
      } else {
        // 🅰️ English or other text directly via jsPDF
        try {
          doc.setFont("OpenSans");
        } catch {
          doc.setFont("helvetica", "normal");
        }
        doc.setFontSize(40);
        doc.text(eventName, pageW / 2, pageH * 0.75, { align: "center" });
      }

      // 🖋️ Participant name
      try {
        doc.setFont("MeaCulpa");
      } catch {
        doc.setFont("helvetica", "bold");
      }
      doc.setFontSize(76);
      doc.text(userName, pageW / 2, pageH * 0.6, { align: "center" });

      // 💾 Save PDF
      const safeName = (userName || "participant").replace(/[^a-z0-9_-]/gi, "_");
      const safeTitle = (eventName || "pledge").replace(/[^a-z0-9_-]/gi, "_");
      doc.save(`${safeName}_${safeTitle}_certificate.pdf`);
    })
    .catch((err) => {
      console.error("Failed to generate certificate", err);
      alert("Could not generate certificate. Please try again.");
    });
};

  return (
    <>
      {/* Breadcrumb Section */}
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="container">
          <div className="breadcurmb-title">
            <h2>Pledge</h2>
          </div>
          <div className="breadcurmb-item-list ul-li">
            <ul className="saasio-page-breadcurmb">
              <li>
                <a href="/">Home</a>
              </li>
              <li>
                <a href="#">Pledge</a>
              </li>
            </ul>
          </div>
        </div>
      </section>

      {/* Result Page */}
      <div className="result-page quiz-font">
        <div className="result-box">
          <img
            src="/design/assets/OBJECTS.png"
            alt="Congrats"
            className="congrats-img"
          />
          <div className="subtext">
            You've successfully Taken the Pledge. Great job!
          </div>
          <div className="participation-text">
            <p>Participation Points: {participationPoints}</p>
          </div>
          {/* <div className="certificate-text">
            🏅{" "}
            <button 
              onClick={handleDownloadCertificate}
              className="download-link pledge-sub"
              style={{ 
                background: 'none', 
                border: 'none', 
                color: 'inherit', 
                textDecoration: 'underline',
                cursor: isGeneratingCert ? 'wait' : 'pointer',
                padding: 0,
                font: 'inherit'
              }}
              disabled={isGeneratingCert}
            >
              {isGeneratingCert ? "Generating..." : "Click here"}
            </button>{" "}
            to download your certificate
          </div> */}
          <button className="try-again-btn pledge-sub margin-0" onClick={() => navigate("/")}>
            Go Home
          </button>
        </div>
      </div>
    </>
  );
};

export default ResultPledgePage;
