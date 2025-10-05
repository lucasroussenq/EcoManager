
window.API_BASE = '/ecomanager/api';


window.apiFetch = async (path, opts = {}) => {
  const full = path.startsWith('http')
    ? path
    : `${API_BASE}${path.startsWith('/') ? path : `/${path}`}`;

  const resp = await fetch(full, {
    credentials: 'include',
    cache: 'no-store',
    ...opts
  });

  if (resp.status === 401 || resp.status === 403) {
    const onAuth = location.pathname.includes('/public/auth/');
    if (!onAuth) location.replace('/ecomanager/public/auth/login.html');
    throw new Error('Não autenticado');
  }
  return resp;
};
