document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    function closeMobileMenu() {
        mobileMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeMobileMenu);
    if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    
    const mobileToursToggle = document.querySelector('.mobile-nav-dropdown-toggle');
    if (mobileToursToggle) {
        mobileToursToggle.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelector('.mobile-nav-item-dropdown').classList.toggle('active');
        });
    }
    
    document.querySelectorAll('.mobile-nav-link, .mobile-dropdown-link').forEach(link => {
        link.addEventListener('click', (e) => {
            if (!e.target.closest('.mobile-nav-dropdown-toggle')) {
                closeMobileMenu();
            }
        });
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu?.classList.contains('active')) {
            closeMobileMenu();
        }
    });
    
    const slider = document.getElementById('slider');
    const sliderPrev = document.getElementById('sliderPrev');
    const sliderNext = document.getElementById('sliderNext');
    const sliderDots = document.getElementById('sliderDots');
    
    let currentSlide = 0;
    let slideInterval;
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;
    
    function createSliderDots() {
        if (!sliderDots) return;
        sliderDots.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.classList.add('slider-dot');
            if (i === 0) dot.classList.add('active');
            dot.setAttribute('data-slide', i);
            dot.addEventListener('click', () => {
                goToSlide(i);
                resetSlideInterval();
            });
            sliderDots.appendChild(dot);
        }
    }
    
    function goToSlide(slideIndex) {
        if (!slider) return;
        currentSlide = slideIndex;
        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        document.querySelectorAll('.slider-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }
    
    if (sliderPrev) {
        sliderPrev.addEventListener('click', () => {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            goToSlide(currentSlide);
            resetSlideInterval();
        });
    }
    
    if (sliderNext) {
        sliderNext.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % totalSlides;
            goToSlide(currentSlide);
            resetSlideInterval();
        });
    }
    
    function startSlideInterval() {
        if (totalSlides === 0) return;
        slideInterval = setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            goToSlide(currentSlide);
        }, 5000);
    }
    
    function resetSlideInterval() {
        clearInterval(slideInterval);
        startSlideInterval();
    }
    
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
        sliderContainer.addEventListener('mouseleave', startSlideInterval);
    }
    
    createSliderDots();
    if (totalSlides > 0) startSlideInterval();
    
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.closest('.faq-item');
            item.classList.toggle('active');
        });
    });
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            if (this.classList.contains('dropdown-item') || 
                this.classList.contains('mobile-dropdown-link') ||
                this.getAttribute('href') === '#') {
                return;
            }
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                closeMobileMenu();
                window.scrollTo({ top: targetElement.offsetTop - 80, behavior: 'smooth' });
            }
        });
    });
    
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 100) {
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            }
        }
    });
    
    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');
    const submitBtn = document.getElementById('submitBtn');
    
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return '';
    }
    
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value}; expires=${date.toUTCString()}; path=/`;
    }
    
    function loadSavedData() {
        const savedName = getCookie('saved_name');
        const savedEmail = getCookie('saved_email');
        const savedPhone = getCookie('saved_phone');
        const savedTour = getCookie('saved_tour');
        const savedMessage = getCookie('saved_message');
        
        if (savedName) document.getElementById('name').value = savedName;
        if (savedEmail) document.getElementById('email').value = savedEmail;
        if (savedPhone) document.getElementById('phone').value = savedPhone;
        if (savedTour) document.getElementById('tour').value = savedTour;
        if (savedMessage) document.getElementById('message').value = savedMessage;
    }
    
    function validateForm(data) {
        const errors = {};
        
        if (!data.name.trim()) {
            errors.name = 'Имя обязательно для заполнения';
        } else if (!/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u.test(data.name)) {
            errors.name = 'Имя может содержать только буквы, пробелы и дефисы';
        }
        
        if (!data.email.trim()) {
            errors.email = 'Email обязателен для заполнения';
        } else if (!/^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/.test(data.email)) {
            errors.email = 'Введите корректный email (например: name@domain.com)';
        }
        
        if (!data.phone.trim()) {
            errors.phone = 'Телефон обязателен для заполнения';
        } else if (!/^(\+7|8)[0-9]{10}$/.test(data.phone)) {
            errors.phone = 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX (10 цифр после кода)';
        }
        
        if (!data.tour) {
            errors.tour = 'Выберите интересующий тур';
        }
        
        return errors;
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        field.style.borderColor = '#dc3545';
        field.style.backgroundColor = '#fff8f8';
        
        let errorDiv = field.parentElement.querySelector('.field-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.style.cssText = 'color: #dc3545; font-size: 12px; margin-top: 5px;';
            field.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        field.style.borderColor = '';
        field.style.backgroundColor = '';
        
        const errorDiv = field.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    function clearAllErrors() {
        const fields = ['name', 'email', 'phone', 'tour'];
        fields.forEach(clearFieldError);
    }
    
    function showFormMessage(msg, type) {
        if (!formMessage) return;
        formMessage.innerHTML = msg + '<br><br><button onclick="this.parentElement.style.display=\'none\'" style="background:#4a6fa5; color:white; border:none; padding:5px 15px; border-radius:5px; cursor:pointer; font-size:14px;">✖ Закрыть</button>';
        formMessage.className = `form-message ${type}`;
        formMessage.style.display = 'block';
        
        setTimeout(() => {
            if (formMessage && formMessage.style.display !== 'none') {
                formMessage.style.display = 'none';
            }
        }, 60000);
    }
    
    loadSavedData();
    
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            clearAllErrors();
            
            const formData = {
                name: document.getElementById('name')?.value || '',
                email: document.getElementById('email')?.value || '',
                phone: document.getElementById('phone')?.value || '',
                tour: document.getElementById('tour')?.value || '',
                message: document.getElementById('message')?.value || ''
            };
            
            const errors = validateForm(formData);
            
            if (Object.keys(errors).length > 0) {
                for (const [field, message] of Object.entries(errors)) {
                    showFieldError(field, message);
                }
                showFormMessage('❌ Пожалуйста, исправьте ошибки в форме', 'error');
                return;
            }
            
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправка...';
            
            try {
                const response = await fetch('/project/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    setCookie('saved_name', formData.name, 365);
                    setCookie('saved_email', formData.email, 365);
                    setCookie('saved_phone', formData.phone, 365);
                    setCookie('saved_tour', formData.tour, 365);
                    setCookie('saved_message', formData.message, 365);
                    
                    contactForm.reset();
                    loadSavedData();
                    
                    const login = result.data.login;
                    const password = result.data.password;
                    
                    showFormMessage(
                        `✅ Заявка отправлена!<br><br>
                        <strong>Ваши данные для входа:</strong><br>
                        Логин: <code>${login}</code><br>
                        Пароль: <code>${password}</code><br><br>
                        <small>Сохраните эти данные. Вы сможете редактировать свою заявку после авторизации.</small>`,
                        'success'
                    );
                    
                    const authLink = document.createElement('div');
                    authLink.style.marginTop = '15px';
                    authLink.innerHTML = '<a href="#login" id="showLoginForm" style="color: #4a6fa5; text-decoration: underline;">Перейти к авторизации →</a>';
                    formMessage.appendChild(authLink);
                    
                    document.getElementById('showLoginForm')?.addEventListener('click', (e) => {
                        e.preventDefault();
                        showLoginModal();
                    });
                    
                } else {
                    if (result.errors) {
                        for (const [field, message] of Object.entries(result.errors)) {
                            if (field !== 'general') {
                                showFieldError(field, message);
                            }
                        }
                        showFormMessage('❌ Пожалуйста, исправьте ошибки в форме', 'error');
                    } else {
                        showFormMessage('❌ Ошибка отправки. Попробуйте позже.', 'error');
                    }
                }
                
            } catch (error) {
                console.error('Ошибка:', error);
                showFormMessage('❌ Ошибка соединения с сервером. Попробуйте позже.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
    
    function showLoginModal() {
        const modal = document.createElement('div');
        modal.id = 'loginModal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        `;
        
        modal.innerHTML = `
            <div style="background: white; padding: 30px; border-radius: 15px; max-width: 400px; width: 90%;">
                <h3 style="margin-bottom: 20px;">🔐 Авторизация</h3>
                <form id="loginFormModal">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Логин:</label>
                        <input type="text" id="modalLogin" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Пароль:</label>
                        <input type="password" id="modalPassword" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <button type="submit" style="width: 100%; padding: 12px; background: #4a6fa5; color: white; border: none; border-radius: 5px; cursor: pointer;">Войти</button>
                    <button type="button" id="closeModal" style="width: 100%; margin-top: 10px; padding: 10px; background: #ccc; border: none; border-radius: 5px; cursor: pointer;">Закрыть</button>
                </form>
                <div id="modalMessage" style="margin-top: 15px; font-size: 14px;"></div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const loginForm = document.getElementById('loginFormModal');
        const closeBtn = document.getElementById('closeModal');
        
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const login = document.getElementById('modalLogin').value;
            const password = document.getElementById('modalPassword').value;
            const msgDiv = document.getElementById('modalMessage');
            
            msgDiv.innerHTML = 'Вход...';
            
            try {
                const response = await fetch('/project/login-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ login, password })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    msgDiv.innerHTML = '✅ Успешный вход! Перенаправление...';
                    localStorage.setItem('travel_user', JSON.stringify(result.user));
                    setTimeout(() => {
                        modal.remove();
                        showEditForm(result.user);
                    }, 1000);
                } else {
                    msgDiv.innerHTML = '❌ ' + (result.error || 'Неверный логин или пароль');
                    msgDiv.style.color = 'red';
                }
            } catch (error) {
                msgDiv.innerHTML = '❌ Ошибка соединения';
                msgDiv.style.color = 'red';
            }
        });
        
        closeBtn.addEventListener('click', () => modal.remove());
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });
    }
    
    function showEditForm(user) {
        const editDiv = document.createElement('div');
        editDiv.id = 'editForm';
        editDiv.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        `;
        
        editDiv.innerHTML = `
            <div style="background: white; padding: 30px; border-radius: 15px; max-width: 500px; width: 90%; max-height: 80%; overflow-y: auto;">
                <h3 style="margin-bottom: 20px;">✏️ Редактирование заявки</h3>
                <form id="editFormModal">
                    <div style="margin-bottom: 15px;">
                        <label>Имя:</label>
                        <input type="text" id="editName" value="${escapeHtml(user.name)}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Email:</label>
                        <input type="email" id="editEmail" value="${escapeHtml(user.email)}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Телефон:</label>
                        <input type="text" id="editPhone" value="${escapeHtml(user.phone || '')}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Тур:</label>
                        <select id="editTour" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Выберите тур</option>
                            <option value="paris" ${user.tour === 'paris' ? 'selected' : ''}>Романтический Париж</option>
                            <option value="japan" ${user.tour === 'japan' ? 'selected' : ''}>Загадочная Япония</option>
                            <option value="iceland" ${user.tour === 'iceland' ? 'selected' : ''}>Исландия</option>
                            <option value="thailand" ${user.tour === 'thailand' ? 'selected' : ''}>Тропический Таиланд</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label>Сообщение:</label>
                        <textarea id="editMessage" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px;">${escapeHtml(user.message || '')}</textarea>
                    </div>
                    <button type="submit" style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Сохранить изменения</button>
                    <button type="button" id="closeEdit" style="width: 100%; margin-top: 10px; padding: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Закрыть</button>
                </form>
                <div id="editMessageDiv" style="margin-top: 15px;"></div>
            </div>
        `;
        
        document.body.appendChild(editDiv);
        
        const editForm = document.getElementById('editFormModal');
        const closeEdit = document.getElementById('closeEdit');
        
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msgDiv = document.getElementById('editMessageDiv');
            
            const updatedData = {
                name: document.getElementById('editName').value,
                email: document.getElementById('editEmail').value,
                phone: document.getElementById('editPhone').value,
                tour: document.getElementById('editTour').value,
                message: document.getElementById('editMessage').value
            };
            
            msgDiv.innerHTML = 'Сохранение...';
            
            try {
                const response = await fetch(`/project/update.php?user_id=${user.id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(updatedData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    msgDiv.innerHTML = '✅ Данные успешно обновлены!';
                    msgDiv.style.color = 'green';
                    setTimeout(() => editDiv.remove(), 1500);
                } else {
                    msgDiv.innerHTML = '❌ Ошибка обновления';
                    msgDiv.style.color = 'red';
                }
            } catch (error) {
                msgDiv.innerHTML = '❌ Ошибка соединения';
                msgDiv.style.color = 'red';
            }
        });
        
        closeEdit.addEventListener('click', () => editDiv.remove());
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    
    const navMenu = document.querySelector('.nav-desktop');
    if (navMenu && !document.querySelector('.nav-link[href="#login"]')) {
        const loginItem = document.createElement('li');
        loginItem.className = 'nav-item';
        loginItem.innerHTML = '<a href="#login" class="nav-link" id="loginNavBtn"><i class="fas fa-sign-in-alt"></i> Войти</a>';
        navMenu.appendChild(loginItem);
        
        document.getElementById('loginNavBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            showLoginModal();
        });
    }
    
    const mobileNav = document.querySelector('.mobile-nav-menu');
    if (mobileNav && !document.querySelector('.mobile-nav-link[href="#login"]')) {
        const mobileLoginItem = document.createElement('li');
        mobileLoginItem.className = 'mobile-nav-item';
        mobileLoginItem.innerHTML = '<a href="#login" class="mobile-nav-link" id="mobileLoginBtn"><i class="fas fa-sign-in-alt"></i><span>Войти</span></a>';
        mobileNav.appendChild(mobileLoginItem);
        
        document.getElementById('mobileLoginBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            closeMobileMenu();
            showLoginModal();
        });
    }
    
    console.log('WorldTravel инициализирован с валидацией');
});