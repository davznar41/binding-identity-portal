const mobileToggle = /** @type {HTMLButtonElement|null} */ (document.querySelector('.mobile-toggle'));
  const navigation = /** @type {HTMLElement|null} */ (document.querySelector('#documentation-navigation'));
  const groupToggle = /** @type {HTMLButtonElement|null} */ (document.querySelector('.group-toggle'));
  const children = /** @type {HTMLElement|null} */ (document.querySelector('#authority-children'));

  if (groupToggle && children) {
    /** @param {boolean} expanded */
    const setGroup = (expanded) => {
      groupToggle.setAttribute('aria-expanded', String(expanded));
      children.hidden = !expanded;
    };
    groupToggle.hidden = false;
    setGroup(groupToggle.dataset.defaultExpanded === 'true');
    groupToggle.addEventListener('click', () => setGroup(groupToggle.getAttribute('aria-expanded') !== 'true'));
  }

  if (mobileToggle && navigation) {
    const breakpoint = window.matchMedia('(max-width: 59.99rem)');
    /** @param {boolean} expanded */
    const setMobile = (expanded, restoreFocus = false) => {
      mobileToggle.setAttribute('aria-expanded', String(expanded));
      navigation.hidden = !expanded;
      if (restoreFocus) mobileToggle.focus();
    };
    const adapt = () => {
      // El cambio de tamaño debe mantener el foco fuera del contenido que se oculta.
      const containedFocus = navigation.contains(document.activeElement);
      const toggleHadFocus = document.activeElement === mobileToggle;
      mobileToggle.hidden = !breakpoint.matches;
      setMobile(!breakpoint.matches || containedFocus);
      if (!breakpoint.matches && toggleHadFocus) {
        navigation.querySelector('a')?.focus();
      }
    };
    mobileToggle.addEventListener('click', () => setMobile(mobileToggle.getAttribute('aria-expanded') !== 'true'));
    navigation.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && breakpoint.matches) {
        event.preventDefault();
        setMobile(false, true);
      }
    });
    mobileToggle.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && breakpoint.matches) setMobile(false);
    });
    breakpoint.addEventListener('change', adapt);
    adapt();
  }
