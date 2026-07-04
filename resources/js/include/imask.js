export function imask() {
    const maskOptions = {
        mask: [
            { mask: '+{7} (000) 000-00-00', startsWith: '7' },
            { mask: '+000000000000000',      startsWith: '' },
        ],
        dispatch(appended, dynamicMasked) {
            const digits = (dynamicMasked.value + appended).replace(/\D/g, '');
            return dynamicMasked.compiledMasks.find(m => m.startsWith && digits.startsWith(m.startsWith))
                ?? dynamicMasked.compiledMasks.at(-1);
        },
    };
    document.querySelectorAll('.imask').forEach(el => el._iMask = new IMask(el, maskOptions));
}
