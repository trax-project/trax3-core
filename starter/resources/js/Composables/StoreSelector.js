import { usePage } from '@inertiajs/vue3'

export function useStoreSelector() {

    const page = usePage();

    const storesChoice = () => {
        return page.props.stores.choice;
    }

    const currentStore = () => {
        // Check that the memorized store is still in the available stores.
        if (localStorage.currentStore
            && page.props.stores.choice.find(item => item.id === localStorage.currentStore)
        ) {
            return localStorage.currentStore;
        }
        return page.props.stores.default;
    }

    const selectStore = (slug) => {
        localStorage.currentStore = slug;
        window.location.reload();
    };

    return { 
        storesChoice,
        currentStore,
        selectStore,    
     };
}
