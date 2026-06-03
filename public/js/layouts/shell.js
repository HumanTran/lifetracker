



document.addEventListener('hide.bs.modal', function (event) {
            event.target.classList.add('modal-is-hiding');
        });

        document.addEventListener('hidden.bs.modal', function (event) {
            event.target.classList.remove('modal-is-hiding');
        });

(function () {
            const sidebar = document.getElementById('mainSidebar');
            const mobileToggleBtn = document.getElementById('mobileSidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (!sidebar) return;

            const mobileQuery = window.matchMedia('(max-width: 991.98px)');

            function isMobile() {
                return mobileQuery.matches;
            }

            function openMobileSidebar() {
                sidebar.classList.add('mobile-open');
                document.body.classList.add('mobile-sidebar-open');
                mobileToggleBtn?.setAttribute('aria-expanded', 'true');
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('mobile-open');
                document.body.classList.remove('mobile-sidebar-open');
                mobileToggleBtn?.setAttribute('aria-expanded', 'false');
            }

            mobileToggleBtn?.addEventListener('click', function () {
                if (sidebar.classList.contains('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            });

            backdrop?.addEventListener('click', closeMobileSidebar);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMobileSidebar();
                }
            });

            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (isMobile()) {
                        closeMobileSidebar();
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    closeMobileSidebar();
                }
            });
        })();

(function () {
            function moveModalsToBody() {
                document.querySelectorAll('.main-container .modal, .content-body .modal').forEach(function (modal) {
                    document.body.appendChild(modal);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', moveModalsToBody);
            } else {
                moveModalsToBody();
            }
        })();

(function () {
            let thumbY = null;
            let thumbX = null;
            let activeScrollEl = null;
            let hideTimer = null;
            let ticking = false;

            function createThumbs() {
                if (thumbY && thumbX) return;

                thumbY = document.createElement('div');
                thumbY.className = 'lt-scroll-thumb-y';

                thumbX = document.createElement('div');
                thumbX.className = 'lt-scroll-thumb-x';

                document.body.appendChild(thumbY);
                document.body.appendChild(thumbX);
            }

            function getRect(el) {
                if (el === document.scrollingElement || el === document.documentElement || el === document.body) {
                    return {
                        top: 0,
                        left: 0,
                        right: window.innerWidth,
                        bottom: window.innerHeight,
                        width: window.innerWidth,
                        height: window.innerHeight
                    };
                }

                return el.getBoundingClientRect();
            }

            function hasVerticalScroll(el) {
                return el && el.scrollHeight > el.clientHeight + 2;
            }

            function hasHorizontalScroll(el) {
                return el && el.scrollWidth > el.clientWidth + 2;
            }

            function updateScrollbar(el) {
                createThumbs();

                if (!el) return;

                const rect = getRect(el);
                const canY = hasVerticalScroll(el);
                const canX = hasHorizontalScroll(el);

                if (canY) {
                    const ratioY = el.clientHeight / el.scrollHeight;
                    const thumbHeight = Math.max(28, rect.height * ratioY);
                    const maxTop = rect.height - thumbHeight;
                    const scrollableY = el.scrollHeight - el.clientHeight;
                    const top = rect.top + ((el.scrollTop / scrollableY) * maxTop);

                    thumbY.style.height = `${thumbHeight}px`;
                    const safeTop = Math.max(rect.top + 2, Math.min(top, rect.bottom - thumbHeight - 2));

                    thumbY.style.left = `${rect.right - 6}px`;
                    thumbY.style.top = `${safeTop}px`;
                    thumbY.classList.add('is-visible');
                } else {
                    thumbY.classList.remove('is-visible');
                }

                if (canX) {
                    const ratioX = el.clientWidth / el.scrollWidth;
                    const thumbWidth = Math.max(28, rect.width * ratioX);
                    const maxLeft = rect.width - thumbWidth;
                    const scrollableX = el.scrollWidth - el.clientWidth;
                    const left = rect.left + ((el.scrollLeft / scrollableX) * maxLeft);

                    thumbX.style.width = `${thumbWidth}px`;
                    const safeLeft = Math.max(rect.left + 2, Math.min(left, rect.right - thumbWidth - 2));

                    thumbX.style.left = `${safeLeft}px`;
                    thumbX.style.top = `${rect.bottom - 6}px`;
                    thumbX.classList.add('is-visible');
                } else {
                    thumbX.classList.remove('is-visible');
                }
            }

            function hideScrollbarLater() {
                clearTimeout(hideTimer);

                hideTimer = setTimeout(function () {
                    if (thumbY) thumbY.classList.remove('is-visible');
                    if (thumbX) thumbX.classList.remove('is-visible');
                }, 450);
            }

            function handleScroll(event) {
                const el = event.target;

                if (!hasVerticalScroll(el) && !hasHorizontalScroll(el)) {
                    return;
                }

                activeScrollEl = el;

                if (!ticking) {
                    requestAnimationFrame(function () {
                        updateScrollbar(activeScrollEl);
                        ticking = false;
                    });

                    ticking = true;
                }

                hideScrollbarLater();
            }

            document.addEventListener('scroll', handleScroll, true);

            window.addEventListener('resize', function () {
                if (activeScrollEl) {
                    updateScrollbar(activeScrollEl);
                    hideScrollbarLater();
                }
            });
        })();
