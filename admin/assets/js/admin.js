// ============================================
// admin.js - Panel Admin
// ============================================

document.addEventListener('DOMContentLoaded', function() {

    // ========================================
    // Tooltips Bootstrap
    // ========================================
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // ========================================
    // SweetAlert: Confirmar toggle de docente
    // ========================================
    document.querySelectorAll('[data-action="toggle-docente"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const activo = this.dataset.activo === '1';

            Swal.fire({
                title: activo ? '¿Desactivar docente?' : '¿Activar docente?',
                html: `Vas a ${activo ? 'desactivar' : 'activar'} a <strong>${nombre}</strong>.<br>
                       <small class="text-muted">El docente ${activo ? 'no podrá marcar asistencia hasta que lo reactives.' : 'podrá volver a marcar asistencia.'}</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: activo ? 'Sí, desactivar' : 'Sí, activar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: activo ? '#ef4444' : '#10b981',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/asistencia-api/admin/docentes/toggle/${id}`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // ========================================
    // Toggle de contraseñas (múltiples)
    // ========================================
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    // ========================================
    // Mostrar / ocultar contraseña (formulario de crear docente)
    // ========================================
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    }

    // ========================================
    // Barra de fortaleza de contraseña (perfil)
    // ========================================
    const passwordNueva = document.getElementById('password_nueva');
    if (passwordNueva) {
        const bar = document.getElementById('strengthBar');
        const text = document.getElementById('strengthText');

        passwordNueva.addEventListener('input', function() {
            const v = this.value;

            if (v.length === 0) {
                bar.style.display = 'none';
                return;
            }

            bar.style.display = 'block';

            let score = 0;
            if (v.length >= 6) score++;
            if (v.length >= 10) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[0-9]/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;

            const level = score <= 2 ? 'weak' : score <= 3 ? 'medium' : 'strong';
            const texts = {
                weak: '🔴 Contraseña débil',
                medium: '🟡 Contraseña aceptable',
                strong: '🟢 Contraseña fuerte'
            };

            bar.className = 'password-strength strength-' + level;
            text.textContent = texts[level];
        });
    }

    // ========================================
    // Formulario de cambio de contraseña (perfil)
    // ========================================
    const formPassword = document.getElementById('formPassword');
    if (formPassword) {
        formPassword.addEventListener('submit', function(e) {
            e.preventDefault();

            const actual    = document.getElementById('password_actual');
            const nueva     = document.getElementById('password_nueva');
            const confirmar = document.getElementById('password_confirmar');

            let errores = [];

            if (!actual.value) {
                errores.push('Debes ingresar tu contraseña actual');
            }
            if (!nueva.value) {
                errores.push('La nueva contraseña es obligatoria');
            } else if (nueva.value.length < 6) {
                errores.push('La nueva contraseña debe tener al menos 6 caracteres');
            } else if (nueva.value === actual.value) {
                errores.push('La nueva contraseña debe ser diferente a la actual');
            }
            if (nueva.value !== confirmar.value) {
                errores.push('Las contraseñas no coinciden');
            }

            if (errores.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Revisa el formulario',
                    html: '<ul class="text-start mb-0" style="font-size: 0.9rem;">' +
                          errores.map(e => `<li>${e}</li>`).join('') +
                          '</ul>',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            Swal.fire({
                title: '¿Cambiar contraseña?',
                html: 'Vas a actualizar tu contraseña de acceso.<br>' +
                      '<small class="text-muted">Deberás usarla en tu próximo inicio de sesión.</small>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#2a5298',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    formPassword.submit();
                }
            });
        });
    }

    // ========================================
    // Validaciones del formulario de Docente
    // ========================================
    const formDocente = document.getElementById('formDocente');
    if (formDocente) {
        inicializarValidaciones(formDocente);
    }

    // ========================================   ← AGREGAR AQUÍ
    // SweetAlert: Aprobar/Rechazar justificación
    // ========================================
    document.querySelectorAll('[data-action="responder"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const accion = this.dataset.accion;
            const nombre = this.dataset.nombre;
            const esAprobar = accion === 'aprobado';

            Swal.fire({
                title: esAprobar ? '¿Aprobar justificación?' : '¿Rechazar justificación?',
                html: esAprobar
                    ? `La falta de <strong>${nombre}</strong> será marcada como <strong class="text-success">JUSTIFICADA</strong>.<br>
                       <small class="text-muted">Esta acción modificará el estado de la asistencia.</small>`
                    : `La justificación de <strong>${nombre}</strong> será <strong class="text-danger">RECHAZADA</strong>.<br>
                       <small class="text-muted">La ausencia seguirá afectando su récord.</small>`,
                icon: esAprobar ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonText: esAprobar ? 'Sí, aprobar' : 'Sí, rechazar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: esAprobar ? '#10b981' : '#ef4444',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/asistencia-api/admin/justificaciones/responder/${id}`;

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'accion';
                    input.value = accion;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
    // ========================================
    // Menú hamburguesa (móvil)
    // ========================================
    const btnHamburger = document.getElementById('btnHamburger');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function abrirMenu() {
        if (sidebar) sidebar.classList.add('sidebar-abierto');
        if (overlay) overlay.classList.add('activo');
        document.body.style.overflow = 'hidden';
    }

    function cerrarMenu() {
        if (sidebar) sidebar.classList.remove('sidebar-abierto');
        if (overlay) overlay.classList.remove('activo');
        document.body.style.overflow = '';
    }

    if (btnHamburger) {
        btnHamburger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Hamburguesa clickeada');  // Debug

            if (sidebar && sidebar.classList.contains('sidebar-abierto')) {
                cerrarMenu();
            } else {
                abrirMenu();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', cerrarMenu);
    }

    // Cerrar al tocar un link del sidebar
    document.querySelectorAll('.sidebar-nav .nav-item, .sidebar-footer .nav-item').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                cerrarMenu();
            }
        });
    });

    // Cerrar al redimensionar a desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            cerrarMenu();
        }
    });

        // ========================================
    // SweetAlert: Confirmar toggle de turno
    // ========================================
    document.querySelectorAll('[data-action="toggle-turno"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const activo = this.dataset.activo === '1';
            const docentes = parseInt(this.dataset.docentes) || 0;

            let mensaje = activo
                ? `Vas a desactivar el turno <strong>${nombre}</strong>.`
                : `Vas a activar el turno <strong>${nombre}</strong>.`;

            if (activo && docentes > 0) {
                mensaje += `<br><br><strong class="text-danger">⚠️ Hay ${docentes} docente(s) asignado(s) a este turno.</strong><br>
                           <small class="text-muted">Si desactivas el turno, esos docentes no podrán marcar asistencia.</small>`;
            }

            Swal.fire({
                title: activo ? '¿Desactivar turno?' : '¿Activar turno?',
                html: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: activo ? 'Sí, desactivar' : 'Sí, activar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: activo ? '#ef4444' : '#10b981',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/asistencia-api/admin/turnos/toggle/${id}`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // ========================================
    // SweetAlert: Eliminar Backup
    // ========================================
    document.querySelectorAll('[data-action="eliminar-backup"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const nombre = this.dataset.nombre;

            Swal.fire({
                title: '¿Eliminar backup?',
                html: `Vas a eliminar el archivo <strong>${nombre}</strong>.<br>
                       <small class="text-muted">Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/asistencia-api/admin/backups/eliminar/${nombre}`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // ========================================
    // SweetAlert: Confirmar toggle de usuario
    // ========================================
    document.querySelectorAll('[data-action="toggle-usuario"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const activo = this.dataset.activo === '1';

            Swal.fire({
                title: activo ? '¿Desactivar usuario?' : '¿Activar usuario?',
                html: `Vas a ${activo ? 'desactivar' : 'activar'} a <strong>${nombre}</strong>.<br>
                       <small class="text-muted">${activo ? 'El usuario no podrá acceder al panel hasta que lo reactives.' : 'El usuario podrá volver a acceder al panel.'}</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: activo ? 'Sí, desactivar' : 'Sí, activar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: activo ? '#ef4444' : '#10b981',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/asistencia-api/admin/usuarios/toggle/${id}`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

});  //cierre del DOMContentLoaded


// ============================================
// FUNCIONES DE VALIDACIÓN + CONFIRMACIÓN (DOCENTE)
// ============================================

function inicializarValidaciones(form) {

    // -------- Utilidad: marcar/desmarcar campo --------
    function marcarError(input, mensaje) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        const feedback = input.parentElement.querySelector('.invalid-feedback')
                       || input.closest('.col-md-3, .col-md-4, .col-md-6, .col-md-12')
                             ?.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = mensaje;
            feedback.style.display = 'block';
        }
    }

    function marcarOk(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }

    // -------- Validadores por campo --------
    const validadores = {

        dni: (input) => {
            const v = input.value.trim();
            if (!v)                       return 'El DNI es obligatorio.';
            if (!/^[0-9]+$/.test(v))      return 'El DNI solo debe contener números.';
            if (v.length !== 8)           return 'El DNI debe tener exactamente 8 dígitos.';
            return null;
        },

        nombres: (input) => {
            const v = input.value.trim();
            if (!v)                              return 'Los nombres son obligatorios.';
            if (v.length < 2)                    return 'Mínimo 2 caracteres.';
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(v))
                                                 return 'Solo se permiten letras y espacios.';
            return null;
        },

        apellidos: (input) => {
            const v = input.value.trim();
            if (!v)                              return 'Los apellidos son obligatorios.';
            if (v.length < 2)                    return 'Mínimo 2 caracteres.';
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(v))
                                                 return 'Solo se permiten letras y espacios.';
            return null;
        },

        email: (input) => {
            const v = input.value.trim();
            if (!v) return null;
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v))
                return 'Correo electrónico inválido.';
            return null;
        },

        telefono: (input) => {
            const v = input.value.trim();
            if (!v) return null;
            if (!/^[0-9]+$/.test(v))   return 'El teléfono solo debe contener números.';
            if (v.length < 6)          return 'Mínimo 6 dígitos.';
            if (v.length > 15)         return 'Máximo 15 dígitos.';
            return null;
        },

        username: (input) => {
            const v = input.value.trim();
            if (!v) return 'El usuario es obligatorio.';
            if (v.length < 3)              return 'Mínimo 3 caracteres.';
            if (!/^[a-zA-Z0-9._]+$/.test(v))
                return 'Solo letras, números, puntos y guiones bajos.';
            return null;
        },

        password: (input) => {
            const v = input.value;
            if (!v) return 'La contraseña es obligatoria.';
            if (v.length < 6) return 'Mínimo 6 caracteres.';
            return null;
        },

        turno_id: (input) => {
            const v = input.value;
            if (!v) return 'Debes seleccionar un turno.';
            return null;
        }
        
    };

    // -------- Filtros de entrada --------
    const filtros = {
        dni:      (v) => v.replace(/[^0-9]/g, '').slice(0, 8),
        telefono: (v) => v.replace(/[^0-9]/g, '').slice(0, 15),
        nombres:  (v) => v.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, ''),
        apellidos:(v) => v.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, ''),
        username: (v) => v.replace(/[^a-zA-Z0-9._]/g, '')
    };

    Object.keys(filtros).forEach(campo => {
        const input = form.querySelector(`[name="${campo}"]`);
        if (!input) return;
        input.addEventListener('input', function() {
            const limpio = filtros[campo](this.value);
            if (this.value !== limpio) this.value = limpio;
        });
    });

    // -------- Validación en blur + input --------
    Object.keys(validadores).forEach(campo => {
        const input = form.querySelector(`[name="${campo}"]`);
        if (!input) return;

        input.addEventListener('blur', function() {
            const error = validadores[campo](this);
            if (error) marcarError(this, error);
            else marcarOk(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                const error = validadores[campo](this);
                if (error) marcarError(this, error);
                else marcarOk(this);
            }
        });
    });

    // -------- SUBMIT con confirmación SweetAlert --------
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let primerError = null;
        let hayErrores = false;

        Object.keys(validadores).forEach(campo => {
            const input = form.querySelector(`[name="${campo}"]`);
            if (!input) return;

            const error = validadores[campo](input);
            if (error) {
                marcarError(input, error);
                hayErrores = true;
                if (!primerError) primerError = input;
            } else {
                marcarOk(input);
            }
        });

        if (hayErrores) {
            primerError.focus();
            primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });

            Swal.fire({
                icon: 'error',
                title: 'Revisa el formulario',
                text: 'Hay campos con errores. Por favor, corrígelos antes de continuar.',
                confirmButtonColor: '#ef4444'
            });
            return;
        }

        const esEdicion = form.action.includes('/editar/');
        const titulo = esEdicion ? '¿Guardar cambios?' : '¿Confirmar registro?';
        const subtitulo = esEdicion
            ? 'Verifica que los datos sean correctos antes de guardar.'
            : 'Verifica los datos antes de registrar al nuevo docente.';

        const preview = construirPreview(form, esEdicion);

        Swal.fire({
            title: titulo,
            html: `
                <p class="text-muted mb-3" style="font-size: 0.9rem;">${subtitulo}</p>
                ${preview}
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: esEdicion
                ? '<i class="bi bi-check-lg"></i> Sí, guardar cambios'
                : '<i class="bi bi-check-lg"></i> Sí, registrar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2a5298',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            customClass: {
                popup: 'swal-preview-popup',
                htmlContainer: 'swal-preview-content'
            },
            width: 520
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // -------- Auto-focus en el primer campo vacío --------
    const primerInput = form.querySelector('input:not([type="hidden"]):not([disabled])');
    if (primerInput && !primerInput.value) {
        primerInput.focus();
    }
}


// ============================================
// CONSTRUIR PREVIEW DE DATOS PARA SWEETALERT
// ============================================
function construirPreview(form, esEdicion) {
    const getVal = (name) => {
        const input = form.querySelector(`[name="${name}"]`);
        return input ? input.value.trim() : '';
    };

    const dni          = getVal('dni');
    const nombres      = getVal('nombres');
    const apellidos    = getVal('apellidos');
    const email        = getVal('email');
    const telefono     = getVal('telefono');
    const especialidad = getVal('especialidad');
    const username     = getVal('username');
    const password     = getVal('password');

    const filas = [];

    const iniciales = (nombres.charAt(0) + apellidos.charAt(0)).toUpperCase();
    filas.push(`
        <div class="preview-header">
            <div class="preview-avatar">${iniciales}</div>
            <div class="preview-name">${nombres} ${apellidos}</div>
        </div>
    `);

    filas.push('<div class="preview-grid">');
    filas.push(`<div class="preview-item"><i class="bi bi-credit-card"></i> <strong>DNI:</strong> ${dni}</div>`);

    if (email) {
        filas.push(`<div class="preview-item"><i class="bi bi-envelope"></i> <strong>Email:</strong> ${email}</div>`);
    }
    if (telefono) {
        filas.push(`<div class="preview-item"><i class="bi bi-phone"></i> <strong>Teléfono:</strong> ${telefono}</div>`);
    }
    if (especialidad) {
        filas.push(`<div class="preview-item"><i class="bi bi-book"></i> <strong>Especialidad:</strong> ${especialidad}</div>`);
    }

    if (!esEdicion) {
        filas.push(`<div class="preview-item"><i class="bi bi-person-badge"></i> <strong>Usuario:</strong> ${username}</div>`);
        filas.push(`<div class="preview-item"><i class="bi bi-key"></i> <strong>Contraseña:</strong> ${'•'.repeat(password.length)}</div>`);
    }

    filas.push('</div>');

    return filas.join('');
}

// ============================================
// SISTEMA DE TOASTS
// ============================================
function mostrarToast(mensaje, tipo = 'success', duracion = 4000) {
    const iconos = {
        success: '✓',
        error: '✕',
        warning: '!',
        info: 'i'
    };

    const icono = iconos[tipo] || iconos.info;

    Toastify({
        text: `<span style="display:inline-flex;align-items:center;gap:10px;">
                 <span style="
                   display:inline-flex;
                   align-items:center;
                   justify-content:center;
                   width:24px;height:24px;
                   background:rgba(255,255,255,0.25);
                   border-radius:50%;
                   font-weight:bold;
                 ">${icono}</span>
                 <span>${mensaje}</span>
               </span>`,
        duration: duracion,
        close: true,
        gravity: 'top',
        position: 'right',
        stopOnFocus: true,
        escapeMarkup: false,
        className: `toastify-${tipo}`,
        offset: {
            x: 20,
            y: 20
        }
    }).showToast();

}