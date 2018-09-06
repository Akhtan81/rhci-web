export default model  => {

    const validator = {
        total: 0,
        errors: []
    }

    if (!model.login) {
        ++validator.total
    } else if (model.login.length < 3) {
        ++validator.total
    }

    if (!model.password) {
        ++validator.total
    } else if (model.password.length < 3) {
        ++validator.total
    }

    return validator
}
