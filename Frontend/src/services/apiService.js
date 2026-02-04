const API_URL = "http://localhost:3000/";

export const getReviews = async () => {
    const res = await fetch(`${API_URL}reviews`);

    return res.json();
  };

export const getReviewsById = async (id) => {
    const res = await fetch(`${API_URL}reviews/${id}`);

    return res.json();
  };

export const createReview = async (review) => {
    const res = await fetch(`${API_URL}reviews`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(review),
    });

    return res.json();
  };        

export const updateReview = async (review) => {
    const res = await fetch(`${API_URL}reviews/${review.id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(review),
    });

    return res.json();
  };    

export const deleteReview = async (id) => {    
    const res = await fetch(`${API_URL}reviews/${id}`, {
      method: "DELETE",
    });

    return res.json();
  }