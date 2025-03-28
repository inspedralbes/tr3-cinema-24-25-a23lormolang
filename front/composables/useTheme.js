export function useTheme() {
  const isDarkMode = ref(false);
  const userTheme = ref(null); // Para almacenar la preferencia del usuario

  // Aplicar el tema al documento
  const applyTheme = (isDark) => {
    const htmlElement = document.documentElement;
    if (isDark) {
      htmlElement.classList.add("dark");
    } else {
      htmlElement.classList.remove("dark");
    }
  };

  // Cargar el tema al montar el componente
  onMounted(() => {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
      // Si hay un tema guardado, usarlo y ignorar la preferencia del sistema
      userTheme.value = savedTheme;
      isDarkMode.value = savedTheme === "dark";
    } else {
      // Si no hay tema guardado, usar la preferencia del sistema
      const systemPrefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)"
      ).matches;
      userTheme.value = systemPrefersDark ? "dark" : "light";
      isDarkMode.value = systemPrefersDark;
    }
    applyTheme(isDarkMode.value);
  });

  // Alternar entre modo oscuro y claro
  const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    userTheme.value = isDarkMode.value ? "dark" : "light";
    applyTheme(isDarkMode.value);
    localStorage.setItem("theme", userTheme.value); // Guardar la preferencia del usuario
  };

  return {
    isDarkMode,
    toggleTheme,
  };
}
