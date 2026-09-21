/**
 * Pediatric age-band gate.
 *
 * Children under 12 belong in `seguimiento_integral_ninez_adolescencia`;
 * adolescents (>= 12) belong in `adolescent_clinical_histories`.
 *
 * Pages that record pediatric history call `PediatricAgeBand.onPatientChange(selectEl, context)`
 * whenever the user picks a patient. The helper computes the patient's age from the
 * `dob` field exposed by `/api/patients_list.php`, marks the page with `data-age-band`
 * ('ninez' / 'adolescencia' / 'unknown'), and hides sections tagged with the wrong band.
 *
 * Page-level markup:
 *   <form data-age-band-allowed="ninez"> ... </form>     // visible only to children < 12
 *   <form data-age-band-allowed="adolescencia"> ... </form> // visible only to >= 12
 *
 * Direct API:
 *   PediatricAgeBand.computeBand(patient)              -> 'ninez' | 'adolescencia' | 'unknown'
 *   PediatricAgeBand.applyBand(context, band)           // hides conflicting sections, surfaces banner
 */
(function () {
  window.PediatricAgeBand = {
    NIÑEZ_CUTOFF: 12,
    BAND_NIÑEZ: 'ninez',
    BAND_ADOLESCENCIA: 'adolescencia',
    BAND_UNKNOWN: 'unknown',

    computeBand(patient) {
      if (!patient || !patient.dob) {
        return this.BAND_UNKNOWN;
      }
      const dob = new Date(patient.dob);
      if (Number.isNaN(dob.getTime())) {
        return this.BAND_UNKNOWN;
      }
      const today = new Date();
      let age = today.getFullYear() - dob.getFullYear();
      const monthDiff = today.getMonth() - dob.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age -= 1;
      }
      return age < this.NIÑEZ_CUTOFF ? this.BAND_NIÑEZ : this.BAND_ADOLESCENCIA;
    },

    /**
     * Apply a band to the page. Sections with a `data-age-band-allowed` attribute that does
     * not match the current band are hidden. A `data-age-band-banner` element gets a
     * warning message when the band is unknown.
     */
    applyBand(context, band) {
      const root = context || document;
      root.setAttribute('data-age-band', band);

      root.querySelectorAll('[data-age-band-allowed]').forEach((el) => {
        const allowed = el.getAttribute('data-age-band-allowed');
        const matches = band === this.BAND_UNKNOWN || allowed === band;
        el.classList.toggle('d-none', !matches);
      });

      const banner = document.querySelector('[data-age-band-banner]');
      if (banner) {
        if (band === this.BAND_UNKNOWN) {
          banner.classList.remove('d-none');
          banner.textContent =
            'El paciente no tiene fecha de nacimiento registrada; no se puede clasificar el formulario.';
        } else {
          banner.classList.add('d-none');
          banner.textContent = '';
        }
      }
    },

    /**
     * Cache of patients keyed by id. Lookup avoids a network round trip per change event.
     */
    cache: new Map(),

    async getPatient(id) {
      if (!id) return null;
      if (this.cache.has(id)) {
        return this.cache.get(id);
      }
      try {
        const res = await fetch(`/api/patient_get.php?id=${encodeURIComponent(id)}`, {
          credentials: 'same-origin',
        });
        if (!res.ok) {
          return null;
        }
        const json = await res.json();
        const patient = json.data || json.patient || null;
        if (patient) {
          this.cache.set(id, patient);
        }
        return patient;
      } catch (err) {
        console.error('PediatricAgeBand.getPatient failed:', err);
        return null;
      }
    },

    /**
     * Wire a `<select>` so changing the patient updates the page's age band.
     */
    async onPatientChange(selectEl, context) {
      if (!selectEl) return;
      const handler = async () => {
        const id = selectEl.value;
        if (!id) {
          this.applyBand(context || document, this.BAND_UNKNOWN);
          return;
        }
        const patient = await this.getPatient(id);
        const band = this.computeBand(patient);
        this.applyBand(context || document, band);
      };
      selectEl.addEventListener('change', handler);
      // Run once on init in case the form re-opens with a patient already selected.
      if (selectEl.value) {
        await handler();
      }
    },
  };
})();
