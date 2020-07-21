import Model from "@/models/Model";
/**
 * User model
 */
// eslint-disable-next-line no-unused-vars
export default class UserModel extends Model{
    // // Default attributes that define the "empty" state.
    defaults() {
        return {
            id: -1,
            name: "",
            email: "",
            email_verified_at: "",
            created_at: "",
            updated_at: '',
            stripe_id: null,
            card_brand: null,
            card_last_four: null,
            trial_ends_at: null,
            access_token: "",
        }
    }
    // mutations() {
    //     return {
    //         id: (id) => Number(id) || null,
    //         name: String,
    //         email: String,
    //         email_verified_at: String  || null,
    //         created_at: String,
    //         updated_at: String,
    //         stripe_id: String  || null,
    //         card_brand: String  || null,
    //         card_last_four: String  || null,
    //         trial_ends_at: String  || null,
    //         access_token: String,
    //     }
    // }
    //
    // validation() {
    //     return {
    //         id:   integer.and(min(1)).or(equal(null)),
    //         name: string.and(required),
    //         email: string.and(required),
    //         access_token: string.and(required),
    //         email_verified_at: String  || null,
    //         created_at: string.and(required),
    //         updated_at: string.and(required),
    //         stripe_id: string.or(equal(null)),
    //         card_brand: string.or(equal(null)),
    //         card_last_four: string.or(equal(null)),
    //         trial_ends_at: string.or(equal(null)),
    //     }
    // }
}
