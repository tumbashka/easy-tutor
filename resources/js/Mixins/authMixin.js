export const AuthMixin = {
    methods: {
        auth() {
            return this.$page.props.auth?.user;
        },
        guest() {
            return !this.auth();
        },
        emailVerified() {
            return this.$page.props.auth?.user?.email_verified_at != null;
        },
        hasRole(role) {
            return this.$page.props.auth?.user?.roles?.includes(role) ?? false;
        },
        isActiveUser() {
            return this.$page.props.auth?.user?.is_active;
        },
        activeLink(pattern) {
            const currentPath = this.$page.url.replace(/^\/+/, '');
            const regexPattern = pattern.replace('*', '.*').replace(/^\/+/, '');
            const regex = new RegExp('^' + regexPattern + '$');
            return regex.test(currentPath);
        },
        can(permission) {
            return this.$page.props.auth?.user?.permissions?.includes(permission) ?? false;
        }
    }
};
