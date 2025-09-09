import {
    Settings, ArrowUpDown, ChartPie, SquareMenu,
    CreditCard, Wallet, PiggyBank, Bitcoin,
    ReceiptText, Utensils, TrendingUp, Banknote,
    Clapperboard, Landmark, TrendingDown, Home,
    User, Bell, Mail, Lock, Eye, EyeOff,
    Calendar, Clock, Search, Plus, Minus,
    Edit, Trash, Save, Download, Upload,
    Star, Heart, Share, Copy, Link,
} from "lucide-vue-next"

export function useIcons() {
    const iconMap: Record<string, any> = {
        // Navigation & UI
        'Settings': Settings,
        'SquareMenu': SquareMenu,
        'Home': Home,
        'User': User,
        'Bell': Bell,
        'Mail': Mail,

        // Finance
        'ArrowUpDown': ArrowUpDown,
        'ChartPie': ChartPie,
        'CreditCard': CreditCard,
        'Wallet': Wallet,
        'PiggyBank': PiggyBank,
        'Bitcoin': Bitcoin,
        'Banknote': Banknote,
        'Landmark': Landmark,
        'TrendingUp': TrendingUp,
        'TrendingDown': TrendingDown,

        // Categories
        'ReceiptText': ReceiptText,
        'Utensils': Utensils,
        'Clapperboard': Clapperboard,

        // Actions
        'Edit': Edit,
        'Trash': Trash,
        'Save': Save,
        'Download': Download,
        'Upload': Upload,
        'Copy': Copy,
        'Share': Share,
        'Link': Link,

        // Utils
        'Calendar': Calendar,
        'Clock': Clock,
        'Search': Search,
        'Plus': Plus,
        'Minus': Minus,
        'Eye': Eye,
        'EyeOff': EyeOff,
        'Lock': Lock,
        'Star': Star,
        'Heart': Heart,
    }

    /**
     * Get icon component by his name.
     *
     * @param iconName
     * @returns component
     */
    function getIcon(iconName: string) {
        return iconMap[iconName] || SquareMenu
    }

    /**
     * Check if an icon exist in the mapping.
     *
     * @param iconName
     * @returns
     */
    function hasIcon(iconName: string): boolean {
        return iconName in iconMap
    }

    /**
     * Get all available icons.
     *
     * @returns Icons names.
     */
    function getAvailableIcons(): string[] {
        return Object.keys(iconMap)
    }


    return {
        getIcon,
        hasIcon,
        getAvailableIcons,
    }
}
