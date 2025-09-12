export function useCssVar() {

    function getCssVar(name: string) {
        return getComputedStyle(document.documentElement)
            .getPropertyValue(name)
            .trim();
    }

    return {
        getCssVar,
    }
}
