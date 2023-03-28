import { extend } from 'vee-validate';
import { max, min, numeric, required, email, size, confirmed, double, ext, regex, alpha_num, integer, length } from 'vee-validate/dist/rules';

extend('min', {
    validate(value, { number }) {
        return Number(value) >= Number(number);
    },
    params: ['number'],
    message: 'This field must have at least {number}.'
})
extend('max', {
    validate(value, { number }) {
        return Number(value) <= Number(number);
    },
    params: ['number'],
    message: 'This field must have less than or equal to {number}.'
})
extend('numeric', {
    ...numeric,
    message: 'This field may only contain numeric characters'
});
extend('required', {
    ...required,
    //message: '{_field_} is required'
    message: (fieldName) => {
        return `${fieldName != '{field}' ? fieldName : 'This field'} is required`
    }
});
extend("email", {
    ...email,
    message: "This field must be a valid email"
});

extend('minmax', {
    validate(value, { min, max }) {
        return Number(value) >= Number(min) && Number(value) <= Number(max)
    },
    params: ['min', 'max'],
    message: '{_field_} must be at least {min} and {max} at most'
})

extend("size", {
    ...size,
    message: "Avatar size should be less than 3 MB!"
});

extend('confirmed', {
    ...confirmed,
    message: 'Password did not match'
});

extend('double', {
    ...double,
    // message: "This field only contain decimal"
    message: (fieldName, placeholders) => {
        return `This field must be a valid decimal` + (placeholders.decimals > 0 ? ` with ${placeholders.decimals} decimal places` : '')
    }
});

extend('ext', {
    ...ext,
    message: "Invalid file extension"
});

extend('regex', {
    ...regex,
    message: "Must be a correct format"
});

extend('alpha_num', {
    ...alpha_num,
    message: "This field may only contain alpha-numeric characters"
});

extend('integer', {
    ...integer,
    message: "Must be integer"
})

extend('length', {
    validate(value, { char_length }) {
        return value.length >= char_length;
    },
    params: ['char_length'],
    message: 'This field must have at least {char_length} characters'
})

// import { extend } from "vee-validate"
// import { required, digits, email, max, regex } from 'vee-validate/dist/rules'

//   extend('digits', {
//     ...digits,
//     message: '{_field_} needs to be {length} digits. ({_value_})',
//   })

//   extend('required', {
//     ...required,
//     message: '{_field_} can not be empty',
//   })

//   extend('max', {
//     ...max,
//     message: '{_field_} may not be greater than {length} characters',
//   })

//   extend('regex', {
//     ...regex,
//     message: '{_field_} {_value_} does not match {regex}',
//   })

//   extend('email', {
//     ...email,
//     message: 'Email must be valid',
//   })