/* Progressive enhancement: server-rendered content and newsletter work without JS. */
(() => {
  "use strict";
  document.documentElement.classList.add("js");
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const toggle = document.querySelector(".menu-toggle");
  const nav = document.getElementById("primary-nav");
  const setMenu = (open) => {
    if (!toggle || !nav) return;
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute(
      "aria-label",
      open ? "Close navigation" : "Open navigation",
    );
    nav.classList.toggle("is-open", open);
  };
  toggle?.addEventListener("click", () =>
    setMenu(toggle.getAttribute("aria-expanded") !== "true"),
  );
  nav?.addEventListener("click", (event) => {
    if (event.target.closest("a")) setMenu(false);
  });
  document.addEventListener("keydown", (event) => {
    if (
      event.key === "Escape" &&
      toggle?.getAttribute("aria-expanded") === "true"
    ) {
      setMenu(false);
      toggle.focus();
    }
  });
  document.addEventListener("click", (event) => {
    if (
      nav?.classList.contains("is-open") &&
      !nav.contains(event.target) &&
      !toggle.contains(event.target)
    )
      setMenu(false);
  });
  document
    .querySelector("[data-dismiss-banner]")
    ?.addEventListener("click", () => {
      document.querySelector("[data-banner]")?.remove();
      document.querySelector(".brand")?.focus();
    });

  const openDialog = (dialog, opener) => {
    if (!dialog || typeof dialog.showModal !== "function") return;
    dialog._estateinOpener = opener;
    dialog.showModal();
    document.body.classList.add("has-dialog");
  };
  document.querySelectorAll("dialog").forEach((dialog) => {
    dialog.addEventListener("close", () => {
      if (!document.querySelector("dialog[open]"))
        document.body.classList.remove("has-dialog");
      if (dialog._estateinOpener?.isConnected)
        dialog._estateinOpener.focus({ preventScroll: true });
    });
    dialog.addEventListener("click", (event) => {
      if (event.target !== dialog) return;
      const rect = dialog.getBoundingClientRect();
      if (
        event.clientX < rect.left ||
        event.clientX > rect.right ||
        event.clientY < rect.top ||
        event.clientY > rect.bottom
      )
        dialog.close();
    });
  });
  document.addEventListener("click", (event) => {
    const opener = event.target.closest("[data-open]");
    if (opener)
      openDialog(document.getElementById(opener.dataset.open), opener);
    const closer = event.target.closest("[data-close]");
    if (closer) {
      // The enquiry link leaves both the details and any underlying collection modal.
      if (closer.tagName === "A")
        document
          .querySelectorAll("dialog[open]")
          .forEach((dialog) => dialog.close());
      else closer.closest("dialog")?.close();
    }
    const collectionOpener = event.target.closest("[data-collection]");
    if (collectionOpener) {
      const section = document.getElementById(
        collectionOpener.dataset.collection,
      );
      const dialog = document.getElementById("collection-dialog");
      if (!section || !dialog) return;
      dialog.querySelector("#collection-title").textContent =
        section.querySelector("h2").textContent;
      const target = dialog.querySelector("[data-collection-content]");
      target.replaceChildren(
        ...Array.from(section.querySelectorAll("[data-card]"), (card) => {
          const copy = card.cloneNode(true);
          copy.removeAttribute("data-card");
          return copy;
        }),
      );
      openDialog(dialog, collectionOpener);
      dialog.querySelector("h2").focus();
    }
  });

  document.querySelectorAll("[data-carousel]").forEach((section) => {
    const track = section.querySelector("[data-track]");
    const cards = Array.from(track.querySelectorAll("[data-card]"));
    const current = section.querySelector("[data-current]");
    const total = section.querySelector("[data-total]");
    const previous = section.querySelector('[data-direction="-1"]');
    const next = section.querySelector('[data-direction="1"]');
    const pad = (number) => String(number).padStart(2, "0");
    const update = () => {
      const step =
        cards.length > 1
          ? cards[1].offsetLeft - cards[0].offsetLeft
          : track.clientWidth;
      const index = Math.max(0, Math.round(track.scrollLeft / step));
      current.textContent = pad(index + 1);
      total.textContent = `of ${pad(cards.length)}`;
      previous.disabled = track.scrollLeft < 2;
      next.disabled =
        track.scrollLeft >= track.scrollWidth - track.clientWidth - 2;
    };
    section.querySelectorAll("[data-direction]").forEach((button) =>
      button.addEventListener("click", () => {
        const step =
          cards.length > 1
            ? cards[1].offsetLeft - cards[0].offsetLeft
            : track.clientWidth;
        track.scrollBy({
          left: step * Number(button.dataset.direction),
          behavior: reducedMotion.matches ? "instant" : "smooth",
        });
      }),
    );
    track.addEventListener("scroll", update, { passive: true });
    new ResizeObserver(update).observe(track);
    update();
  });

  const form = document.getElementById("newsletter");
  form?.addEventListener("submit", async (event) => {
    if (!window.fetch || !form.reportValidity()) return;
    event.preventDefault();
    const button = form.querySelector('button[type="submit"]');
    const message = document.getElementById("newsletter-message");
    const input = form.querySelector('[name="email"]');
    button.disabled = true;
    form.setAttribute("aria-busy", "true");
    message.textContent = "Saving your subscription…";
    input.removeAttribute("aria-invalid");
    try {
      const response = await fetch(form.dataset.ajaxUrl, {
        method: "POST",
        body: new FormData(form),
        credentials: "same-origin",
        headers: { Accept: "application/json" },
      });
      const result = await response.json();
      if (typeof result.message !== "string")
        throw new Error("Unexpected response");
      message.textContent = result.message;
      if (result.status === "invalid") {
        input.setAttribute("aria-invalid", "true");
        input.focus();
      }
      if (result.success) input.value = "";
    } catch {
      message.textContent =
        "We could not confirm your subscription. Please check your connection and try again.";
    } finally {
      button.disabled = false;
      form.removeAttribute("aria-busy");
    }
  });
})();
