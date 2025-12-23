<template>
  <div class="page">
    <!-- Header section -->
    <header class="header">
      <div class="flex">
        <a href="/home" class="logo">
          Kape Na! <i class="fas fa-coffee"></i>
        </a>

        <nav class="navbar">
          <a href="/home">Home</a>
          <a href="/Auth/login">Menu</a>
          <a href="/Auth/login">Orders</a>
          <a href="/Auth/login">About</a>
        </nav>

        <div class="auth-links">
          <nav class="navbar">
            <a href="/Auth/login">Login</a>
            <a href="/Auth/register">Register</a>
          </nav>
        </div>
      </div>
    </header>

    <!-- showcase area -->
    <section class="hero">
      <div class="swiper hero-slider">
        <div class="swiper-wrapper">
          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>Turmeric Spiced Coffee</h3>
              <a href="/Auth/login" class="btn">see menus</a>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-1.1.png"
                alt="Turmeric Spiced Coffee"
              />
            </div>
          </div>

          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>delicious pizza</h3>
              <a href="/Auth/login" class="btn">see menus</a>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-1.png"
                alt="Delicious Pizza"
              />
            </div>
          </div>

          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>chezzy hamburger</h3>
              <a href="/Auth/login" class="btn">see menus</a>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-2.png"
                alt="Cheesy Hamburger"
              />
            </div>
          </div>

          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>roasted chicken</h3>
              <a href="/Auth/login" class="btn">see menus</a>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-3.png"
                alt="Roasted Chicken"
              />
            </div>
          </div>
        </div>

        <div class="swiper-pagination"></div>
      </div>
    </section>

    <!-- category Area -->
    <section class="category">
      <h1 class="title">food category</h1>

      <div class="box-container">
        <a href="/Auth/login" class="box">
          <img
            src="http://localhost:8000/images/cat-1.png"
            alt="Coffee"
          />
          <h3>Coffee</h3>
        </a>

        <a href="/Auth/login" class="box">
          <img
            src="http://localhost:8000/images/cat-2.png"
            alt="Special Dishes"
          />
          <h3>Special dishes</h3>
        </a>

        <a href="/Auth/login" class="box">
          <img
            src="http://localhost:8000/images/cat-3.png"
            alt="Drinks"
          />
          <h3>drinks</h3>
        </a>

        <a href="/Auth/login" class="box">
          <img
            src="http://localhost:8000/images/cat-4.png"
            alt="Desserts"
          />
          <h3>desserts</h3>
        </a>
      </div>
    </section>

    <!-- about section starts  -->
    <section class="about">
      <div class="row">
        <div class="image">
          <img
            src="http://localhost:8000/images/coffee-shop-1209863_1280.jpg"
            alt="Why Choose Us"
          />
        </div>
        <div class="content">
          <h3>why choose us?</h3>
          <p>
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Deserunt,
            neque debitis incidunt qui ipsum sed doloremque a molestiae in
            veritatis ullam similique sunt aliquam dolores dolore? Quasi atque
            debitis nobis!
          </p>
          <a href="/Auth/login" class="btn">our menu</a>
        </div>
      </div>
    </section>

    <!-- steps section starts  -->
    <section class="steps">
      <h1 class="title">simple steps</h1>
      <div class="box-container">
        <div class="box">
          <img
            src="http://localhost:8000/images/step-1.png"
            alt="Choose Order"
          />
          <h3>choose order</h3>
          <p>
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nesciunt,
            dolorem.
          </p>
        </div>

        <div class="box">
          <img
            src="http://localhost:8000/images/step-2.png"
            alt="Fast Delivery"
          />
          <h3>fast delivery</h3>
          <p>
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nesciunt,
            dolorem.
          </p>
        </div>

        <div class="box">
          <img
            src="http://localhost:8000/images/step-3.png"
            alt="Enjoy Food"
          />
          <h3>enjoy food</h3>
          <p>
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nesciunt,
            dolorem.
          </p>
        </div>
      </div>
    </section>

    <!-- Map / contact -->
    <section class="contact">
      <div class="row">
        <div class="map">
          <iframe
            class="minmap"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15777.467380763725!2d125.60962806977538!3d8.956199999999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3301ceb3eb7f5e0f%3A0x4a5f5b04a5b5b5b5!2sCaraga%20State%20University%20-%20Ampayon%20Campus!5e1!3m2!1sen!2sbd!4v1660587920897!5m2!1sen!2sbd"
            width="600"
            height="450"
            style="border: 0"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          />
        </div>

        <!-- Simple static contact form; backend handling is in Laravel -->
        <form
          action="http://localhost:8000/message/store"
          method="post"
        >
          <h3>Contact Us !</h3>
          <input
            type="text"
            name="name"
            maxlength="50"
            class="box"
            placeholder="Enter Your Name"
            required
          />
          <input
            type="email"
            name="email"
            maxlength="50"
            class="box"
            placeholder="Enter Your Email"
            required
          />
          <textarea
            name="msg"
            class="box"
            required
            placeholder="Enter Your Message"
            maxlength="500"
            cols="30"
            rows="10"
          />
          <input type="submit" value="send" name="send" class="btn" />
        </form>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <section class="grid">
        <div class="box">
          <h3>Cafe Shop <i class="fas fa-shopping-basket"></i></h3>
          <div class="share">
            <a href="https://www.facebook.com/jemhail" class="fab fa-facebook-f"></a>
            <a
              href="https://www.instagram.com/jembo.magbanua/"
              class="fab fa-instagram"
            ></a>
            <a
              href="http://linkedin.com/in/jembo-magbanua-b49b6b2b2/"
              class="fab fa-linkedin"
            ></a>
          </div>
        </div>

        <div class="box">
          <h3>Opening Hours</h3>
          <div class="dateinfo">
            <p>MONDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>TUESDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>WEDNESDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>THURSDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>FRIDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>SATURDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>SUNDAY</p>
            <samp>CLOSE!</samp>
          </div>
        </div>

        <div class="box">
          <h3>Contact Info</h3>
          <div class="phone">
            <p><i class="fas fa-phone"></i> +639914677225</p>
            <p><i class="fas fa-phone"></i> +639512592678</p>
            <p><i class="fas fa-envelope"></i> magbanuajembo17@gmail.com</p>
          </div>

          <div class="phone1">
            <h3>Branch Location</h3>
            <p>
              <i class="fas fa-map-marker-alt"></i> Caraga State University - Main
              Campus
            </p>
            <p><i class="fas fa-envelope"></i> magbanuajembo17@gmail.com</p>
          </div>
        </div>
      </section>

      <div class="credit">
        &copy; copyright @ {{ new Date().getFullYear() }} by
        <span>Cafe Shop</span> |
        <a>Magbanua, Jembo</a> | all rights reserved!
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useHead } from '#imports';

// Load Swiper assets for hero slider pagination
useHead({
  link: [
    {
      rel: 'stylesheet',
      href: 'https://unpkg.com/swiper@8/swiper-bundle.min.css',
    },
  ],
  script: [
    {
      src: 'https://unpkg.com/swiper@8/swiper-bundle.min.js',
      defer: true,
    },
  ],
});

// Initialize Swiper hero slider with clickable pagination
onMounted(() => {
  const initSwiper = () => {
    // Ensure Swiper from CDN is loaded
    const SwiperCtor = window.Swiper;
    if (!SwiperCtor) {
      setTimeout(initSwiper, 100);
      return;
    }

    // eslint-disable-next-line no-new
    new SwiperCtor('.hero-slider', {
      loop: true,
      grabCursor: true,
      effect: 'flip',
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
    });
  };

  initSwiper();
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Anton&display=swap');

:root {
  --yellow: black;
  --red: #e74c3c;
  --white: #fff;
  --black: #222;
  --light-color: #777;
  --border: 0.2rem solid var(--black);
  --main-color: #d3ad7f;
  --bg: #856731bd;
  --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  outline: none;
  border: none;
  text-decoration: none;
  font-family: 'Anton', sans-serif;
}

body {
  background: var(--bg);
  color: var(--white);
}

section {
  margin: 0 auto;
  max-width: 1200px;
  padding: 2rem;
}

.title {
  text-align: center;
  margin-bottom: 2rem;
  font-size: 3rem;
  color: var(--white);
  text-transform: uppercase;
  text-underline-offset: 1rem;
}

.btn {
  margin-top: 1rem;
  display: inline-block;
  font-size: 1.6rem;
  padding: 0.8rem 2.4rem;
  cursor: pointer;
  text-transform: capitalize;
  transition: 0.2s linear;
  background-color: var(--yellow);
  color: var(--white);
}

.btn:hover {
  letter-spacing: 0.2rem;
}

.header {
  position: sticky;
  top: 0;
  left: 0;
  right: 0;
  background-color: var(--black);
  z-index: 1000;
  padding: 1.5rem 2rem;
  box-shadow: var(--box-shadow);
}

.flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  font-size: 2rem;
  color: var(--white);
}

.logo i {
  color: var(--main-color);
  margin-right: 0.5rem;
}

.navbar a {
  margin: 0 1rem;
  font-size: 1.4rem;
  color: var(--white);
}

.navbar a:hover {
  color: var(--main-color);
}

.hero {
  width: 100%;
  margin-bottom: 100px;
}

.hero .swiper-slide {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  gap: 1.5rem;
  padding-top: 6rem;
}

.hero .slide .content {
  flex: 0 0 40%;
  text-align: center;
}

.hero .slide .image {
  flex: 0 0 40%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero .slide .image img {
  width: 100%;
  max-height: 22rem;
  object-fit: cover;
  border-radius: 1rem;
}

.hero .slide .content span {
  color: var(--main-color);
  font-size: 1.8rem;
}

.hero .slide .content h3 {
  color: var(--white);
  font-size: 4.5rem;
  text-transform: capitalize;
  margin: 1rem 0;
}

.category .box-container {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 1.5rem;
  align-items: flex-start;
}

.category .box-container .box {
  border: var(--border);
  padding: 2rem;
  text-align: center;
  background: var(--black);
  transition: transform 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
}

.category .box-container .box:hover {
  transform: translateY(-10px);
}

.category .box-container .box img {
  width: 100%;
  height: 10rem;
  object-fit: cover;
  border-radius: 0.5rem;
}

.category .box-container .box h3 {
  font-size: 2rem;
  margin-top: 1.5rem;
  color: var(--white);
  text-transform: capitalize;
}

.category .box-container .box:hover {
  background-color: var(--white);
}

.category .box-container .box:hover h3 {
  color: var(--black);
}

.about .row {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  gap: 1.5rem;
  background-color: var(--black);
  padding: 2rem;
}

.about .row .image {
  flex: 1 1 40rem;
}

.about .row .image img {
  width: 100%;
  max-height: 24rem;
  object-fit: cover;
  border-radius: 1rem;
}

.about .row .content {
  flex: 1 1 40rem;
  text-align: center;
}

.about .row .content h3 {
  font-size: 2.4rem;
  color: var(--white);
  text-transform: capitalize;
  margin-bottom: 1rem;
}

.about .row .content p {
  padding: 1rem 0;
  line-height: 2;
  font-size: 1.3rem;
  color: var(--light-color);
}

.steps .box-container {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1.5rem;
  align-items: flex-start;
}

.steps .box-container .box {
  text-align: center;
  padding: 2rem;
  transition: transform 0.3s;
}

.steps .box-container .box:hover {
  transform: translateY(-10px);
}

.steps .box-container .box img {
  height: 12rem;
  width: 100%;
  object-fit: contain;
  margin-bottom: 1rem;
}

.steps .box-container .box h3 {
  font-size: 1.8rem;
  margin: 1rem 0;
  color: var(--white);
  text-transform: capitalize;
}

.steps .box-container .box p {
  line-height: 2;
  font-size: 1.3rem;
  color: var(--light-color);
}

.contact .row {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  gap: 1.5rem;
}

.contact .row .map {
  flex: 1 1 50%;
}

.contact .row .map iframe {
  width: 100%;
  height: 30rem;
  border: var(--border);
  border-radius: 1rem;
}

.contact .row form {
  flex: 1 1 50%;
  padding: 2rem;
  text-align: center;
}

.contact .row form h3 {
  font-size: 1.8rem;
  color: var(--white);
  margin-bottom: 1rem;
  text-transform: capitalize;
}

.contact .row form .box {
  margin: 0.7rem 0;
  font-size: 1.4rem;
  color: var(--white);
  border-bottom: var(--border);
  padding: 1.4rem;
  width: 100%;
  background: transparent;
}

.contact .row form textarea {
  height: 20rem;
  resize: none;
}

.footer {
  background: var(--black);
  padding: 3rem 2rem;
  text-align: center;
  margin-top: 5rem;
}

.footer .grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.5rem;
  align-items: flex-start;
}

.footer .grid .box {
  text-align: center;
  padding: 1.2rem 1rem;
  border: var(--border);
}

.footer .grid .box h3 {
  font-size: 1rem;
  margin-bottom: 1.5rem;
  color: var(--white);
  text-transform: capitalize;
}

.footer .grid .box a,
.footer .grid .box p {
  display: block;
  padding: 0.2rem 0;
  font-size: 1rem;
  color: var(--light-color);
}

.footer .grid .box a:hover {
  color: var(--white);
  text-decoration: underline;
}

.footer .grid .box .share {
  margin-top: 1rem;
}

.footer .grid .box .share a {
  display: inline-block;
  margin: 0 0.5rem;
  height: 4rem;
  width: 4rem;
  line-height: 4rem;
  font-size: 1rem;
  background: var(--white);
  color: var(--black);
  border-radius: 50%;
}

.footer .grid .box .share a:hover {
  background: var(--main-color);
  color: var(--white);
}

.footer .grid .box .dateinfo {
  display: flex;
  justify-content: space-between;
  padding: 0.1rem 0;
}

.footer .grid .box .dateinfo p,
.footer .grid .box .dateinfo samp {
  font-size: 1rem;
  color: var(--light-color);
}

.footer .grid .box .phone p {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.footer .grid .box .phone1 h3 {
  margin-top: 2rem;
}

.footer .credit {
  margin-top: 2.5rem;
  padding: 2rem 1rem;
  border-top: var(--border);
  font-size: 1rem;
  color: var(--white);
}

.footer .credit span {
  color: var(--main-color);
}

.footer .credit a {
  color: var(--main-color);
}

.footer .credit a:hover {
  text-decoration: underline;
}

@media (max-width: 991px) {
  html {
    font-size: 55%;
  }
}

@media (max-width: 768px) {
  .about .row {
    flex-wrap: wrap;
  }

  .header .navbar {
    position: absolute;
    top: 99%;
    left: 0;
    right: 0;
    background-color: var(--black);
    clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
    transition: 0.3s linear;
  }

  .hero .slide .content h3 {
    font-size: 5rem;
  }

  .hero .slide .image {
    flex: 1 1 100%;
  }

  .contact .row {
    flex-direction: column;
  }
}

@media (max-width: 450px) {
  html {
    font-size: 50%;
  }

  .title {
    font-size: 3rem;
  }

  .hero .slide .content h3 {
    font-size: 4rem;
  }

  .category .box-container .box img {
    height: 15rem;
  }
}
</style>

