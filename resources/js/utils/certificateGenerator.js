import { jsPDF } from "jspdf";

/**
 * Generates and downloads a participation certificate PDF
 * 
 * @param {Object} options - Certificate configuration
 * @param {string} options.userName - Name of the participant
 * @param {string} options.eventName - Name of the event/quiz/pledge
 * @param {string} [options.eventType='Event'] - Type of event (Quiz, Pledge, Task, etc.)
 * @param {string} [options.bgImageUrl='/design/assets/certificate/certifiacte-participation.jpg'] - Background image URL
 * @param {string} [options.meeraFontUrl='/font/Meera-Regular.ttf'] - Malayalam font URL
 * @param {string} [options.openSansFontUrl='/font/OpenSans.ttf'] - English font URL
 * @param {string} [options.meaFontUrl='/font/MeaCulpa.ttf'] - Decorative font URL
 * @returns {Promise<void>}
 */
export const generateCertificate = async ({
  userName,
  eventName,
  eventType = 'Event',
  bgImageUrl = '/design/assets/certificate/certifiacte-participation.jpg',
  meeraFontUrl = '/font/Meera-Regular.ttf',
  openSansFontUrl = '/font/OpenSans.ttf',
  meaFontUrl = '/font/MeaCulpa.ttf'
}) => {
  
  // Validate required parameters
  if (!userName || !eventName) {
    throw new Error('userName and eventName are required for certificate generation');
  }

  const doc = new jsPDF({
    orientation: "landscape",
    unit: "px",
    format: [1200, 800],
  });

  // Helper: Convert ArrayBuffer to Base64
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

  // Helper: Draw Malayalam text as image using Meera font
  const drawMalayalamTextAsImage = (text, color = "#000000") => {
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    // Set font
    ctx.font = "55px 'Meera Malayalam', 'Meera', sans-serif";
    const textWidth = ctx.measureText(text).width;

    canvas.width = textWidth;
    canvas.height = 120;

    ctx.font = "55px 'Meera Malayalam', 'Meera', sans-serif";
    ctx.fillStyle = color;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(text, canvas.width / 2, canvas.height / 2);

    return { dataUrl: canvas.toDataURL("image/png"), width: textWidth };
  };

  try {
    // Load fonts and image in parallel
    const [meeraBuf, meaBuf, openSansBuf, img] = await Promise.all([
      fetch(meeraFontUrl)
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
        img.src = bgImageUrl;
      }),
    ]);

    // Register fonts in jsPDF
    try {
      if (meeraBuf) {
        const meeraB64 = arrayBufferToBase64(meeraBuf);
        doc.addFileToVFS("Meera-Regular.ttf", meeraB64);
        doc.addFont("Meera-Regular.ttf", "Meera", "normal");
      }
    } catch (err) {
      console.warn("Could not load Meera font, falling back.", err);
    }

    try {
      if (meaBuf) {
        const meaB64 = arrayBufferToBase64(meaBuf);
        doc.addFileToVFS("MeaCulpa.ttf", meaB64);
        doc.addFont("MeaCulpa.ttf", "MeaCulpa", "normal");
      }
    } catch (err) {
      console.warn("Could not load MeaCulpa font, falling back.", err);
    }

    try {
      if (openSansBuf) {
        const openSansB64 = arrayBufferToBase64(openSansBuf);
        doc.addFileToVFS("OpenSans.ttf", openSansB64);
        doc.addFont("OpenSans.ttf", "OpenSans", "normal");
      }
    } catch (err) {
      console.warn("Could not load OpenSans font, falling back.", err);
    }

    // Draw background
    const pageW = doc.internal.pageSize.getWidth();
    const pageH = doc.internal.pageSize.getHeight();

    doc.setFillColor(255, 255, 255);
    doc.rect(0, 0, pageW, pageH, "F");

    const canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);
    const bgDataUrl = canvas.toDataURL("image/jpeg");
    doc.addImage(bgDataUrl, "JPEG", 0, 0, pageW, pageH);

    // Malayalam text detection
    const isMalayalam = /[\u0D00-\u0D7F]/.test(eventName);

    if (isMalayalam) {
      // Draw Malayalam text using Canvas image (centered)
      const { dataUrl, width } = drawMalayalamTextAsImage(eventName);
      const displayWidth = width < 300 ? 300 : width - 50;

      const x = pageW / 2 - displayWidth / 2;
      const y = pageH * 0.715;
      doc.addImage(dataUrl, "PNG", x, y, displayWidth, 52);
    } else {
      // English or other text — normal jsPDF text
      try {
        doc.setFont("OpenSans");
      } catch {
        doc.setFont("helvetica", "normal");
      }
      doc.setFontSize(40);
      doc.text(eventName, pageW / 2, pageH * 0.75, { align: "center" });
    }

    // Render username (English font)
    try {
      doc.setFont("MeaCulpa");
    } catch {
      doc.setFont("helvetica", "bold");
    }
    doc.setFontSize(76);
    doc.text(userName, pageW / 2, pageH * 0.6, { align: "center" });

    // Generate filename
    const safeName = (userName || "participant")
      .trim()
      .replace(/\s+/g, "_") // Replace spaces with underscore
      .replace(/[^a-z0-9_-]/gi, "_") // Replace special chars with underscore
      .replace(/_+/g, "_") // Replace multiple underscores with single
      .toLowerCase();

    const safeTitle = (eventName || eventType)
      .trim()
      .replace(/\s+/g, "_")
      .replace(/[^a-z0-9_-]/gi, "_")
      .replace(/_+/g, "_")
      .toLowerCase();

    const timestamp = new Date().toISOString().split("T")[0]; // YYYY-MM-DD format
    const filename = `${safeName}_${safeTitle}_certificate_${timestamp}.pdf`;

    // Save file
    doc.save(filename);

    console.log(`Certificate generated successfully: ${filename}`);
    return { success: true, filename };

  } catch (err) {
    console.error("Failed to generate certificate", err);
    throw new Error("Could not generate certificate. Please try again.");
  }
};

/**
 * Wrapper function for Quiz certificate generation
 */
export const generateQuizCertificate = (userName, quizTitle, score, totalQuestions) => {
  const eventName = quizTitle || 'Quiz';
  return generateCertificate({
    userName,
    eventName,
    eventType: 'Quiz'
  });
};

/**
 * Wrapper function for Pledge certificate generation
 */
export const generatePledgeCertificate = (userName, pledgeTitle) => {
  const eventName = pledgeTitle || 'Pledge';
  return generateCertificate({
    userName,
    eventName,
    eventType: 'Pledge'
  });
};

/**
 * Wrapper function for Task certificate generation
 */
export const generateTaskCertificate = (userName, taskTitle) => {
  const eventName = taskTitle || 'Task';
  return generateCertificate({
    userName,
    eventName,
    eventType: 'Task'
  });
};
