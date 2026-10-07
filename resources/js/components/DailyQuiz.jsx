"use client"
import { useState } from "react"

export default function DailyQuiz() {
  const [selected, setSelected] = useState(null)
  const [showPopup, setShowPopup] = useState(false)

  const options = [
    { id: "opt_1", label: "sea grass bed", letter: "A" },
    { id: "opt_2", label: "sea grass bed", letter: "B" },
    { id: "opt_3", label: "sea grass bed", letter: "C" },
    { id: "opt_4", label: "sea grass bed", letter: "D" },
  ]

  const handleSubmit = () => {
    if (selected) {
      setShowPopup(true)
    } else {
      alert("Please select an option!")
    }
  }

  return (
    <section className="dailyq it-up-contact-section position-relative d-none">
      {/* Decorative shapes */}
      <img className="quizz" src="/design/assets/qs2.png" alt="quiz deco" />
      <span className="it-up-service-shape position-absolute deco1">
        <img src="/design/assets/vect/s-shape1.png" alt="" />
      </span>
      <span className="it-up-service-shape position-absolute deco2">
        <img src="/design/assets/vect/s-shape2.png" alt="" />
      </span>
      <span className="it-up-service-shape position-absolute deco4">
        <img src="/design/assets/vect/s-shape4.png" alt="" />
      </span>
      <span className="it-up-service-shape position-absolute deco5">
        <img src="/design/assets/vect/s-shape5.png" alt="" />
      </span>

      <div className="container">
        <div className="mt-0 it-up-contact-content">
          <div className="row d-flex align-items-center">
            {/* Left Section */}
            <div className="col-lg-4">
              <div className="it-up-contact-img position-relative">
                <div className="it-nw-section-title headline pera-content">
                  <span className="it-nw-title-tag">Quiz</span>
                  <h2 className="pol">
                    Daily <br />
                    <span className="surv">QUIZ</span>
                  </h2>
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus,
                    luctus nec ullamcorper mattis, pulvinar dapibus leo.
                  </p>
                </div>
              </div>
            </div>

            {/* Right Section */}
            <div className="col-lg-8">
              <div className="it-up-form-wrap">
                <h3>
                  Today&apos;s <br /> Question
                </h3>

                <div className="quiz">
                  <div className="row">
                    <div className="col-12">
                      <div className="qstn">
                        <p>
                          Lorem ipsum dolor sit amet consectetur adipisicing elit. Quo laborum
                          sint necessitatibus, amet praesentium fugit quaerat aspernatur possimus!
                        </p>
                      </div>
                    </div>

                    {options.map((opt) => (
                      <div key={opt.id} className="col-lg-6 col-xl-6 col-md-6 col-12">
                        <div className="lix">
                          <input
                            type="radio"
                            id={opt.id}
                            name="quiz_option"
                            value={opt.label}
                            checked={selected === opt.id}
                            onChange={() => setSelected(opt.id)}
                          />
                          <label htmlFor={opt.id}>{opt.label}</label>
                          <span className="position-absolute">{opt.letter}</span>
                        </div>
                      </div>
                    ))}

                    <div className="col-12">
                      <div className="it-nw-btn d-flex justify-content-center">
                        <button className="openx" onClick={handleSubmit}>
                          Submit
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Popup Modal */}
      {showPopup && (
        <div className="popup" id="popup">
          <img src="/design/assets/congra.png" className="con" alt="congrats" />
          <br />
          <h5>
            Lorem ipsum dolor sit amet consectetur auod esse architecto facilis sit atque rem,
            doloribus mollitia!
          </h5>
          <button className="close" onClick={() => setShowPopup(false)}>
            Claim Reward
          </button>
        </div>
      )}
    </section>
  )
}
