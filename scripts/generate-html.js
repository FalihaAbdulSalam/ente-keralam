const fs = require('fs');
const path = require('path');

const manifestPath = path.join(__dirname, '../public/build/manifest.json');
const htmlTemplatePath = path.join(__dirname, '../public/index.html');

if (!fs.existsSync(manifestPath)) {
    console.error('Manifest file not found at:', manifestPath);
    process.exit(1);
}

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));

// Extract entry assets
const jsEntry = manifest['resources/js/app.jsx'];
const scssEntry = manifest['resources/sass/app.scss'];

let cssTags = [];
let jsTags = [];

if (scssEntry && scssEntry.file) {
    cssTags.push(`<link rel="stylesheet" href="/build/${scssEntry.file}">`);
}

if (jsEntry) {
    if (jsEntry.css && Array.isArray(jsEntry.css)) {
        jsEntry.css.forEach(cssFile => {
            cssTags.push(`<link rel="stylesheet" href="/build/${cssFile}">`);
        });
    }
    if (jsEntry.file) {
        jsTags.push(`<script type="module" src="/build/${jsEntry.file}"></script>`);
    }
}

const htmlContent = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ente Keralam - Government of Kerala Citizen Engagement Platform</title>
    <meta name="description" content="Join Ente Keralam, Kerala's premier citizen engagement platform. Participate in competitions, quizzes, polls, pledges, and discussions. Contribute to Kerala's development and growth.">
    <meta name="keywords" content="Kerala, citizen engagement, competitions, quizzes, polls, pledges, discussions, Government of Kerala, Ente Keralam">
    <meta name="author" content="Government of Kerala - C-DIT">
    <meta name="theme-color" content="#ff176b">

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/site.webmanifest" />

    <!-- Design stylesheets -->
    <link rel="stylesheet" href="/design/css/bootstrap.min.css?v=1.0">
    <link rel="stylesheet" href="/design/css/fontawesome-all.css?v=1.0">
    <link rel="stylesheet" href="/design/css/flaticon-5.css?v=1.0">
    <link rel="stylesheet" href="/design/css/flaticon-34.css?v=1.0">
    <link rel="stylesheet" href="/design/css/animate.css?v=1.0">
    <link rel="stylesheet" href="/design/css/video.min.css?v=1.0">
    <link rel="stylesheet" href="/design/css/slick.css?v=1.0">
    <link rel="stylesheet" href="/design/css/side-demo.css?v=1.0">
    <link rel="stylesheet" href="/design/css/buttons.css?v=1.0.1">
    <link rel="stylesheet" href="/design/css/style-34.css?v=1.0">
    <link rel="stylesheet" href="/design/css/responsive-35.css?v=1.0">
    <link rel="stylesheet" href="/design/css/jquery-ui.css?v=1.0">
    <link rel="stylesheet" href="/design/css/jquery.mCustomScrollbar.min.css?v=1.0">
    <link rel="stylesheet" href="/design/css/sidebar.css?v=1.0">
    <link rel="stylesheet" href="/design/owl.carousel.css?v=1.0">

    <!-- Build stylesheets -->
    ${cssTags.join('\n    ')}

    <!-- Build scripts -->
    ${jsTags.join('\n    ')}
</head>
<body>
    <div id="app"></div>
</body>
</html>
`;

fs.writeFileSync(htmlTemplatePath, htmlContent, 'utf8');
console.log('Successfully generated public/index.html for SPA deployment');
