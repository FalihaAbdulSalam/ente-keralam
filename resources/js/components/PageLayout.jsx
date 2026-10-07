import { useLocation, matchPath } from "react-router-dom";
import CircleMenu from "./CircleMenu";
import Header from "./Header";
import Footer from "./Footer";
import InnerHeader from "./InnerHeader";
import ScrollTop from "./ScrollTop";

const PageLayout = ({ children }) => {
  const location = useLocation();
  
  const menuItems = [
  { title: "Competition", image: "/design/assets/new/c-comp-red.svg" },
  { title: "Group Discussion", image: "/design/assets/new/c-diss-blue.svg" },
  { title: "Pledge", image: "/design/assets/new/c-pledg-pista.svg" },
  { title: "Poll/Survey", image: "/design/assets/new/c-poll-grn.svg" },
  { title: "Quiz", image: "/design/assets/new/c-qiz-pink.svg" },
  { title: "To-Do Task", image: "/design/assets/new/c-task-sky.svg" },
  { title: "Pledge", image: "/design/assets/new/c-pledg-pista.svg" },
  { title: "Group Discussion", image: "/design/assets/new/c-diss-blue.svg" },
];

  // Check if the current route is the Quiz page
  // const hideCircleMenu = location.pathname === "/quiz/:id";
  const hideCircleMenu = matchPath("/quiz/:id", location.pathname);

  return (
    <div className="page-layout">
      {/* <InnerHeader /> */}
      <Header />
      <main>{children}</main>
      {!hideCircleMenu && <CircleMenu items={menuItems} />}
      <ScrollTop />
      <Footer />
    </div>
  );
  
};

export default PageLayout;

