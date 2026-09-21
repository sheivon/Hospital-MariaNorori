class SeguimientoPediatricDataLayer {
  static async request(url, options = {}) {
    const defaultOptions = { credentials: 'same-origin' };
    const res = await fetch(url, { ...defaultOptions, ...options });
    const json = await res.json();
    if (!json.success) throw new Error(json.error || 'Server Error');
    return json;
  }

  static list() {
    return this.request('/api/seguimiento_pediatric_list.php');
  }

  static get(id) {
    return this.request(`/api/seguimiento_pediatric_get.php?id=${encodeURIComponent(id)}`);
  }

  static create(payload) {
    return this.request('/api/seguimiento_pediatric_create.php', { method: 'POST', body: JSON.stringify(payload), headers: {'Content-Type': 'application/json'} });
  }

  static update(payload) {
    return this.request('/api/seguimiento_pediatric_update.php', { method: 'POST', body: JSON.stringify(payload), headers: {'Content-Type': 'application/json'} });
  }

  static delete(id) {
    return this.request('/api/seguimiento_pediatric_delete.php', { method: 'POST', body: JSON.stringify({ id }), headers: {'Content-Type': 'application/json'} });
  }
}

window.SeguimientoPediatricDataLayer = SeguimientoPediatricDataLayer;