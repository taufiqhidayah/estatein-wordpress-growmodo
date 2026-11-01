/* Full-site forms, office tabs, and optional property photo galleries. */
(() => {
  'use strict';
  document.querySelectorAll('[data-inquiry-form]').forEach(form => {
    const status = form.querySelector('.inquiry-status');
    const submit = form.querySelector('button[type="submit"]');
    const updatePreferredContact = () => {
      const extraPhone = form.querySelector('[name="contact_phone"]');
      if (!extraPhone) return;
      extraPhone.required = form.querySelector('[name="preferred_contact"]:checked')?.value === 'phone' && !form.querySelector('[name="phone"]').value.trim();
    };
    form.addEventListener('change', updatePreferredContact);
    form.addEventListener('input', updatePreferredContact);
    updatePreferredContact();
    form.addEventListener('submit', async event => {
      if (!window.fetch) return;
      event.preventDefault();
      if (form.getAttribute('aria-busy') === 'true' || !form.reportValidity()) return;
      form.querySelectorAll('[aria-invalid]').forEach(field => field.removeAttribute('aria-invalid'));
      form.setAttribute('aria-busy', 'true');
      submit.disabled = true;
      status.textContent = 'Saving your enquiry…';
      try {
        const response = await fetch(form.dataset.ajaxUrl, {method:'POST',body:new FormData(form),credentials:'same-origin',headers:{Accept:'application/json'}});
        const result = await response.json();
        if (typeof result.message !== 'string') throw new Error('Unexpected response');
        const errors = result.fields && typeof result.fields === 'object' ? result.fields : {};
        status.textContent = [result.message, ...new Set(Object.values(errors).filter(message => typeof message === 'string'))].join(' ');
        let firstInvalid;
        for (const name of Object.keys(errors)) {
          const fieldName = name === 'full_name' ? 'first_name' : name;
          const field = Array.from(form.elements).find(element => element.name === fieldName);
          if (field) { field.setAttribute('aria-invalid','true'); firstInvalid ||= field; }
        }
        if (result.success) { form.reset(); updatePreferredContact(); status.focus(); }
        else if (firstInvalid) firstInvalid.focus();
        else status.focus();
      } catch {
        status.textContent = 'We could not confirm your enquiry. Check your connection and try again.';
        status.focus();
      } finally {
        form.removeAttribute('aria-busy');
        submit.disabled = false;
      }
    });
  });
  document.querySelectorAll('[data-offices]').forEach(section => {
    const tabs = Array.from(section.querySelectorAll('[data-office-filter]'));
    const panel = section.querySelector('#office-results');
    const choose = tab => {
      tabs.forEach(item => { const selected = item === tab; item.setAttribute('aria-selected',String(selected)); item.tabIndex = selected ? 0 : -1; });
      let count = 0;
      section.querySelectorAll('[data-office-region]').forEach(card => {
        card.hidden = tab.dataset.officeFilter !== 'all' && tab.dataset.officeFilter !== card.dataset.officeRegion;
        if (!card.hidden) count++;
      });
      panel.setAttribute('aria-labelledby',tab.id);
      section.querySelector('[data-office-status]').textContent = `${count} ${count === 1 ? 'office' : 'offices'} shown`;
    };
    tabs.forEach((tab,index) => {
      tab.addEventListener('click',() => choose(tab));
      tab.addEventListener('keydown',event => {
        let next;
        if (event.key === 'ArrowRight') next = (index+1)%tabs.length;
        if (event.key === 'ArrowLeft') next = (index-1+tabs.length)%tabs.length;
        if (event.key === 'Home') next = 0;
        if (event.key === 'End') next = tabs.length-1;
        if (next !== undefined) { event.preventDefault(); choose(tabs[next]); tabs[next].focus(); }
      });
    });
  });
  document.querySelectorAll('[data-property-gallery]').forEach(gallery => {
    const slides = Array.from(gallery.querySelectorAll('[data-gallery-slide]'));
    const buttons = Array.from(gallery.querySelectorAll('[data-gallery-index]'));
    buttons.forEach(button => button.addEventListener('click',() => {
      const current = Number(button.dataset.galleryIndex);
      slides.forEach((slide,index) => { slide.hidden = index !== current; });
      buttons.forEach(item => item.setAttribute('aria-pressed',String(item === button)));
    }));
  });
})();
