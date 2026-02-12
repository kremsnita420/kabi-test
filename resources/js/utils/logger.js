// Small logger wrapper (easy to disable in production)
export const log = (...args) => {
  // eslint-disable-next-line no-console
  console.log(...args);
};
