// import React from "react";
// import { Swiper, SwiperSlide } from "swiper/react";
// import { Navigation, Pagination, Autoplay } from "swiper/modules";
// import "swiper/css";
// import "swiper/css/navigation";
// import "swiper/css/pagination";
// import "./EconomySlider.css";

// const data = [
//   {
//     title: "Sample Heading",
//     img: "/design/assets/new/no1.png",
//   },
//   {
//     title: "Sample Heading",
//     img: "/design/assets/new/no3.png",
//   },
//   {
//     title: "Sample Heading Here",
//     img: "/design/assets/new/no2.png",
//   },
//   {
//     title: "Sample Heading",
//     img: "/design/assets/new/no3.png",
//   },
//   {
//     title: "Sample Heading",
//     img: "/design/assets/new/no1.png",
//   },
//   {
//     title: "Sample",
//     img: "/design/assets/new/no3.png",
//   },
// ];

// const KeralaNumberOneSlider = () => {
//   return (
//     <div id="it-nw-blog" className="it-nw-blog-section position-relative">
//       <div className="it-nw-blog-sh position-absolute">
//         <img src="/design/assets/its-2/blg-bg.png" alt="background" />
//       </div>

//       <div className="container">
//         <div className="it-nw-blog-top-wrap d-flex justify-content-between align-items-center">
//           <div className="it-nw-section-title headline pera-content">
//             <span className="it-nw-title-tag">NO:1</span>
//             <h2>Kerala Number One</h2>
//             <p>
//               incididunt ut labore et dolore magna aliqua. Quis ipsum
//               suspendisse ultrices gravida. Risus commodo viverra maecenas
//               accumsan lacus vel facilisis.
//             </p>
//           </div>
//           <div className="it-nw-btn text-center">
//             <a
//               className="d-flex justify-content-center align-items-center"
//               href="#"
//             >
//               View More <i className="fas fa-arrow-right ms-2"></i>
//             </a>
//           </div>
//         </div>

//         <div className="it-nw-blog-content">
//           {/* <Swiper
//             modules={[Navigation, Pagination, Autoplay]}
//             slidesPerView={3}
//             spaceBetween={30}
//             loop={true}
//             autoplay={{ delay: 2500, disableOnInteraction: false }}
//             pagination={{ clickable: true }}
//             breakpoints={{
//               0: { slidesPerView: 1 },
//               768: { slidesPerView: 2 },
//               1024: { slidesPerView: 3 },
//             }}
//           >
//             {data.map((item, index) => (
//               <SwiperSlide key={index}>
//                 <div className="it-nw-blog-innerbox">
//                   <img
//                     src="/design/assets/new/tr-t.svg"
//                     className="tr-t trio"
//                     alt="triangle"
//                   />
//                   <img
//                     src="/design/assets/new/tr-b.svg"
//                     className="tr-b trio"
//                     alt="triangle"
//                   />
//                   <div className="it-nw-blog-inner-text1 headline">
//                     <h4 className="vertH">{item.title}</h4>
//                   </div>
//                   <div className="it-nw-blog-inner-img">
//                     <img src={item.img} alt={item.title} />
//                   </div>
//                   <div className="vertR">
//                     <a href="#">Read More</a>
//                   </div>
//                   <div className="sicons">
//                     <a href="#">
//                       <img src="/design/assets/social/facebook.svg" alt="fb" />
//                     </a>
//                     <a href="#">
//                       <img src="/design/assets/social/insta.svg" alt="insta" />
//                     </a>
//                     <a href="#">
//                       <img src="/design/assets/social/whatsapp.svg" alt="whatsapp" />
//                     </a>
//                     <a href="#">
//                       <img src="/design/assets/social/twitter1.svg" alt="twitter" />
//                     </a>
//                   </div>
//                 </div>
//               </SwiperSlide>
//             ))}
//           </Swiper> */}
//           <Swiper
//   modules={[Navigation, Pagination, Autoplay]}
//   slidesPerView={3}
//   slidesPerGroup={3}
//   spaceBetween={30}
//   loop={true}
//   autoplay={{ delay: 2500, disableOnInteraction: false }}
//   pagination={{ clickable: true }}
//   breakpoints={{
//     0: { slidesPerView: 1, slidesPerGroup: 1 },
//     768: { slidesPerView: 2, slidesPerGroup: 2 },
//     1024: { slidesPerView: 3, slidesPerGroup: 3 },
//   }}
// >
//   {data.map((item, index) => (
//     <SwiperSlide key={index}>
//       <div className="it-nw-blog-innerbox">
//         <img src="/design/assets/new/tr-t.svg" className="tr-t trio" alt="triangle" />
//         <img src="/design/assets/new/tr-b.svg" className="tr-b trio" alt="triangle" />
//         <div className="it-nw-blog-inner-text1 headline">
//           <h4 className="vertH">{item.title}</h4>
//         </div>
//         <div className="it-nw-blog-inner-img">
//           <img src={item.img} alt={item.title} />
//         </div>
//         <div className="vertR">
//           <a href="#">Read More</a>
//         </div>
//         <div className="sicons">
//           <a href="#"><img src="/design/assets/social/facebook.svg" alt="fb" /></a>
//           <a href="#"><img src="/design/assets/social/insta.svg" alt="insta" /></a>
//           <a href="#"><img src="/design/assets/social/whatsapp.svg" alt="whatsapp" /></a>
//           <a href="#"><img src="/design/assets/social/twitter1.svg" alt="twitter" /></a>
//         </div>
//       </div>
//     </SwiperSlide>
//   ))}
// </Swiper>

//         </div>
//       </div>
//     </div>
//   );
// };

// export default KeralaNumberOneSlider;
import { useState, useEffect } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import "./EconomySlider.css";

const KeralaNumberOneSlider = ({ articles = [] }) => {
  const [activeGroup, setActiveGroup] = useState(0);
  const [data, setData] = useState([]);

  useEffect(() => {
    if (Array.isArray(articles) && articles.length > 0) {
      const mapped = articles.map((a) => {
        // Build image URL: prefer absolute poster, then poster_folder_name + poster, then fallback
        let img = "/design/assets/new/no1.png";
        if (a.poster) {
          if (/^https?:\/\//i.test(a.poster)) {
            img = a.poster;
          } else if (a.poster_folder_name) {
            img = `/${a.poster}`;
          } else {
            img = a.poster;
          }
        }
        return {
          title: a.maltitle || a.entitle || "",
          img,
          id: a.id,
        };
      });
      setData(mapped);
    } else {
      // fallback static data
      setData([
        { title: "Sample Heading", img: "/design/assets/new/no1.png" },
        { title: "Sample Heading", img: "/design/assets/new/no3.png" },
        { title: "Sample Heading", img: "/design/assets/new/no2.png" },
        { title: "Sample Heading", img: "/design/assets/new/no3.png" },
        { title: "Sample Heading", img: "/design/assets/new/no1.png" },
        { title: "Sample", img: "/design/assets/new/no3.png" },
      ]);
    }
  }, [articles]);

  const totalGroups = Math.ceil(data.length / 3);

  return (
    <div className="it-nw-blog-content">
      <div className="it-nw-blog-slider">
      <Swiper
        modules={[Pagination, Autoplay]}
        slidesPerView={3}
        spaceBetween={10}
        // loop={true}
        // autoplay={{ delay: 2500, disableOnInteraction: false }}
        breakpoints={{
          0: { slidesPerView: 1 },
          768: { slidesPerView: 2 },
          1024: { slidesPerView: 3 },
        }}
        onSlideChange={(swiper) => {
          const groupIndex = Math.floor(swiper.realIndex / 3);
          setActiveGroup(groupIndex);
        }}
      >
        {data.map((item, index) => (
          <SwiperSlide key={index}>
            <div className="it-nw-blog-inner-text1 headline">
                <h4
                  className="vertH vertH1"
                >
                  {item.title}
                </h4>
              </div>
            <div className="it-nw-blog-innerbox-1">
              <img src="/design/assets/new/tr-t.svg" className="tr-t trio" alt="triangle" />
              <img src="/design/assets/new/tr-b.svg" className="tr-b trio" alt="triangle" />
              
              <div className="it-nw-blog-inner-img">
                <img className="img-klano" src={item.img} alt={item.title || 'slide'} />
              </div>
              <div className="vertR">
                <a href={`/dept-detail?id=${item.id || ''}`}>Read More</a>
              </div>
              <div className="sicons">
                <a href="#"><img src="/design/assets/social/facebook.svg" alt="fb" /></a>
                <a href="#"><img src="/design/assets/social/insta.svg" alt="insta" /></a>
                <a href="#"><img src="/design/assets/social/whatsapp.svg" alt="whatsapp" /></a>
                <a href="#"><img src="/design/assets/social/twitter1.svg" alt="twitter" /></a>
              </div>
            </div>
          </SwiperSlide>
        ))}
      </Swiper>

      {/* Custom group-based pagination dots */}
      <div className="custom-pagination">
        {Array.from({ length: totalGroups }).map((_, i) => (
          <span
            key={i}
            className={`custom-dot ${i === activeGroup ? "active" : ""}`}
          ></span>
        ))}
      </div>
      </div>
    </div>
  );
};

export default KeralaNumberOneSlider;
